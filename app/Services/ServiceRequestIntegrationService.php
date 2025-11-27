<?php

namespace App\Services;

use App\Models\Factory;
use App\Models\ProjectLocation;
use App\Models\Service;
use App\Models\Store;
use App\Services\FactoryRecommendationService;
use App\Services\StoreRecommendationService;
use Illuminate\Support\Collection;

class ServiceRequestIntegrationService
{
    protected StoreRecommendationService $storeRecommendationService;
    protected FactoryRecommendationService $factoryRecommendationService;

    public function __construct(
        StoreRecommendationService $storeRecommendationService,
        FactoryRecommendationService $factoryRecommendationService
    ) {
        $this->storeRecommendationService = $storeRecommendationService;
        $this->factoryRecommendationService = $factoryRecommendationService;
    }

    /**
     * Get recommended stores and factories for a service request.
     */
    public function getRecommendationsForService(
        Service $service,
        ?float $latitude = null,
        ?float $longitude = null,
        ?ProjectLocation $projectLocation = null
    ): array {
        $recommendations = [
            'stores' => collect(),
            'factories' => collect(),
        ];

        // Get location coordinates
        if ($projectLocation && $projectLocation->hasCoordinates()) {
            $latitude = $projectLocation->latitude;
            $longitude = $projectLocation->longitude;
        }

        if (!$latitude || !$longitude) {
            return $recommendations;
        }

        // Get store recommendations based on service category and location
        $recommendedStores = $this->storeRecommendationService->getRecommendations(
            $latitude,
            $longitude,
            5,
            50
        );

        // Get factory recommendations (all types) based on location
        $recommendedFactories = $this->factoryRecommendationService->getRecommendations(
            $latitude,
            $longitude,
            9,
            100
        );

        // Filter stores/factories based on service category if needed
        // This can be enhanced with category-based filtering
        $recommendations['stores'] = $recommendedStores->take(5);
        $recommendations['factories'] = $recommendedFactories->take(9);

        return $recommendations;
    }

    /**
     * Get material suggestions for a service based on service category.
     */
    public function getMaterialSuggestionsForService(Service $service): array
    {
        $suggestions = [];

        // Map service categories to suggested materials
        $categoryMaterialMap = [
            'konstruksi' => ['semen', 'pasir', 'bata', 'besi', 'beton'],
            'renovasi' => ['cat', 'keramik', 'granit', 'kayu', 'baja'],
            'pondasi' => ['beton', 'besi', 'semen', 'pasir'],
            'struktur' => ['baja', 'beton', 'besi', 'kayu'],
            'atap' => ['genting', 'baja', 'kayu'],
            'lantai' => ['keramik', 'granit', 'semen'],
        ];

        $categoryName = strtolower($service->category->name ?? '');
        
        foreach ($categoryMaterialMap as $key => $materials) {
            if (str_contains($categoryName, $key)) {
                $suggestions = array_merge($suggestions, $materials);
            }
        }

        // Default suggestions if no match
        if (empty($suggestions)) {
            $suggestions = ['semen', 'pasir', 'bata', 'besi', 'beton'];
        }

        return array_unique($suggestions);
    }

    /**
     * Calculate estimated material costs for a service request.
     */
    public function estimateMaterialCosts(
        Service $service,
        ?float $latitude = null,
        ?float $longitude = null,
        ?ProjectLocation $projectLocation = null
    ): array {
        $suggestions = $this->getMaterialSuggestionsForService($service);
        $estimates = [];

        // Get location coordinates
        if ($projectLocation && $projectLocation->hasCoordinates()) {
            $latitude = $projectLocation->latitude;
            $longitude = $projectLocation->longitude;
        }

        foreach ($suggestions as $material) {
            // Try to find products from stores
            $storeProduct = \App\Models\StoreProduct::where('is_active', true)
                ->where(function($query) use ($material) {
                    $query->where('name', 'like', "%{$material}%")
                        ->orWhere('description', 'like', "%{$material}%");
                })
                ->with('store')
                ->first();

            // Try to find products from factories
            $factoryProduct = \App\Models\FactoryProduct::where('is_available', true)
                ->where(function($query) use ($material) {
                    $query->where('name', 'like', "%{$material}%")
                        ->orWhere('description', 'like', "%{$material}%");
                })
                ->with('factory')
                ->first();

            $estimate = [
                'material' => $material,
                'store_price' => null,
                'factory_price' => null,
                'store_delivery_cost' => 0,
                'factory_delivery_cost' => 0,
                'recommended_source' => null,
            ];

            if ($storeProduct) {
                $estimate['store_price'] = $storeProduct->final_price;
                $estimate['store_name'] = $storeProduct->store->name;
                
                // Calculate delivery cost if location provided
                if ($latitude && $longitude && $storeProduct->store) {
                    // Delivery cost calculation can be added here
                    $estimate['store_delivery_cost'] = 0; // Placeholder
                }
            }

            if ($factoryProduct) {
                $estimate['factory_price'] = $factoryProduct->final_price;
                $estimate['factory_name'] = $factoryProduct->factory->name;
                
                // Calculate delivery cost if location provided
                if ($latitude && $longitude && $factoryProduct->factory) {
                    $nearestLocation = $factoryProduct->factory->locations
                        ->where('is_active', true)
                        ->filter(function($location) {
                            return $location->hasCoordinates();
                        })
                        ->map(function($location) use ($latitude, $longitude) {
                            $distance = \App\Helpers\GeolocationHelper::calculateDistance(
                                $latitude,
                                $longitude,
                                $location->latitude,
                                $location->longitude
                            );
                            return [
                                'location' => $location,
                                'distance' => $distance,
                            ];
                        })
                        ->sortBy('distance')
                        ->first();

                    if ($nearestLocation && $factoryProduct->factory->delivery_price_per_km) {
                        $estimate['factory_delivery_cost'] = \App\Helpers\GeolocationHelper::calculateDeliveryCost(
                            $nearestLocation['distance'],
                            $factoryProduct->factory->delivery_price_per_km
                        );
                    }
                }
            }

            // Determine recommended source
            if ($estimate['store_price'] && $estimate['factory_price']) {
                $storeTotal = $estimate['store_price'] + $estimate['store_delivery_cost'];
                $factoryTotal = $estimate['factory_price'] + $estimate['factory_delivery_cost'];
                $estimate['recommended_source'] = $storeTotal < $factoryTotal ? 'store' : 'factory';
            } elseif ($estimate['store_price']) {
                $estimate['recommended_source'] = 'store';
            } elseif ($estimate['factory_price']) {
                $estimate['recommended_source'] = 'factory';
            }

            $estimates[] = $estimate;
        }

        return $estimates;
    }
}

