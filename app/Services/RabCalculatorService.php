<?php

namespace App\Services;

use App\Helpers\GeolocationHelper;
use App\Models\Factory;
use App\Models\FactoryProduct;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Services\FactoryRecommendationService;
use App\Services\StoreRecommendationService;
use Illuminate\Support\Collection;

class RabCalculatorService
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
     * Calculate RAB with integrated store and factory data.
     */
    public function calculateWithIntegration(
        array $items,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $projectLocationId = null
    ): array {
        $calculatedItems = [];
        $totalCost = 0;
        $storeItems = [];
        $factoryItems = [];
        $deliveryCosts = [];
        $recommendations = [];

        foreach ($items as $item) {
            $itemName = strtolower($item['name'] ?? '');
            $quantity = $item['quantity'] ?? 0;
            $unit = $item['unit'] ?? '';

            // Try to find matching products from stores
            $storeProduct = $this->findStoreProduct($itemName, $unit);
            
            // Try to find matching products from factories
            $factoryProduct = $this->findFactoryProduct($itemName, $unit);

            $itemData = [
                'name' => $item['name'],
                'quantity' => $quantity,
                'unit' => $unit,
                'source' => 'manual',
                'unit_price' => $item['unit_price'] ?? 0,
                'subtotal' => $quantity * ($item['unit_price'] ?? 0),
                'store_product' => null,
                'factory_product' => null,
                'delivery_cost' => 0,
                'total_cost' => 0,
            ];

            // If store product found, use store price
            if ($storeProduct) {
                $itemData['source'] = 'store';
                $itemData['store_product'] = $storeProduct;
                $itemData['unit_price'] = $storeProduct->final_price;
                $itemData['subtotal'] = $quantity * $storeProduct->final_price;
                $storeItems[] = $itemData;
                
                // Calculate delivery cost if location provided
                if ($latitude && $longitude && $storeProduct->store) {
                    $deliveryCost = $this->calculateStoreDeliveryCost(
                        $storeProduct->store,
                        $latitude,
                        $longitude,
                        $quantity
                    );
                    $itemData['delivery_cost'] = $deliveryCost;
                    $itemData['total_cost'] = $itemData['subtotal'] + $deliveryCost;
                    $deliveryCosts[] = $deliveryCost;
                } else {
                    $itemData['total_cost'] = $itemData['subtotal'];
                }
            }
            // If factory product found, use factory price
            elseif ($factoryProduct) {
                $itemData['source'] = 'factory';
                $itemData['factory_product'] = $factoryProduct;
                $itemData['unit_price'] = $factoryProduct->final_price;
                $itemData['subtotal'] = $quantity * $factoryProduct->final_price;
                $factoryItems[] = $itemData;
                
                // Calculate delivery cost if location provided
                if ($latitude && $longitude && $factoryProduct->factory) {
                    $deliveryCost = $this->calculateFactoryDeliveryCost(
                        $factoryProduct->factory,
                        $latitude,
                        $longitude,
                        $quantity
                    );
                    $itemData['delivery_cost'] = $deliveryCost;
                    $itemData['total_cost'] = $itemData['subtotal'] + $deliveryCost;
                    $deliveryCosts[] = $deliveryCost;
                } else {
                    $itemData['total_cost'] = $itemData['subtotal'];
                }
            } else {
                // Manual price
                $itemData['total_cost'] = $itemData['subtotal'];
            }

            $calculatedItems[] = $itemData;
            $totalCost += $itemData['total_cost'];
        }

        // Get recommendations if location provided
        if ($latitude && $longitude) {
            $recommendations = $this->getRecommendations($latitude, $longitude, $storeItems, $factoryItems);
        }

        return [
            'items' => $calculatedItems,
            'total_cost' => $totalCost,
            'subtotal' => array_sum(array_column($calculatedItems, 'subtotal')),
            'total_delivery_cost' => array_sum($deliveryCosts),
            'store_items_count' => count($storeItems),
            'factory_items_count' => count($factoryItems),
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Find matching store product by name and unit.
     */
    private function findStoreProduct(string $itemName, string $unit): ?StoreProduct
    {
        // Search for products matching the item name
        $products = StoreProduct::where('is_active', true)
            ->where(function($query) use ($itemName) {
                $query->where('name', 'like', "%{$itemName}%")
                    ->orWhere('description', 'like', "%{$itemName}%");
            })
            ->where('unit', $unit)
            ->with('store')
            ->get();

        if ($products->isEmpty()) {
            return null;
        }

        // Return the first available product
        return $products->first();
    }

    /**
     * Find matching factory product by name and unit.
     */
    private function findFactoryProduct(string $itemName, string $unit): ?FactoryProduct
    {
        // Search for products matching the item name
        $products = FactoryProduct::where('is_available', true)
            ->where(function($query) use ($itemName) {
                $query->where('name', 'like', "%{$itemName}%")
                    ->orWhere('description', 'like', "%{$itemName}%");
            })
            ->where('unit', $unit)
            ->with('factory')
            ->get();

        if ($products->isEmpty()) {
            return null;
        }

        // Return the first available product
        return $products->first();
    }

    /**
     * Calculate store delivery cost.
     */
    private function calculateStoreDeliveryCost(Store $store, float $latitude, float $longitude, float $quantity): float
    {
        $nearestLocation = $store->locations
            ->where('is_active', true)
            ->filter(function($location) {
                return $location->hasCoordinates();
            })
            ->map(function($location) use ($latitude, $longitude) {
                $distance = GeolocationHelper::calculateDistance(
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

        if (!$nearestLocation) {
            return 0;
        }

        // Stores may not have delivery_price_per_km, use default or calculate based on distance
        // For now, return 0 or use a default calculation
        // This can be enhanced later with store-specific delivery pricing
        return 0; // Store delivery cost calculation can be added later
    }

    /**
     * Calculate factory delivery cost.
     */
    private function calculateFactoryDeliveryCost(Factory $factory, float $latitude, float $longitude, float $quantity): float
    {
        $nearestLocation = $factory->locations
            ->where('is_active', true)
            ->filter(function($location) {
                return $location->hasCoordinates();
            })
            ->map(function($location) use ($latitude, $longitude) {
                $distance = GeolocationHelper::calculateDistance(
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

        if (!$nearestLocation || !$factory->delivery_price_per_km) {
            return 0;
        }

        return GeolocationHelper::calculateDeliveryCost(
            $nearestLocation['distance'],
            $factory->delivery_price_per_km
        );
    }

    /**
     * Get recommendations for stores and factories.
     */
    private function getRecommendations(float $latitude, float $longitude, array $storeItems, array $factoryItems): array
    {
        $recommendations = [
            'stores' => [],
            'factories' => [],
        ];

        // Get store recommendations
        if (!empty($storeItems)) {
            $storeRecommendations = $this->storeRecommendationService->getRecommendations(
                $latitude,
                $longitude,
                5,
                50
            );
            $recommendations['stores'] = $storeRecommendations->take(5)->values();
        }

        // Get factory recommendations (all types)
        if (!empty($factoryItems)) {
            $factoryRecommendations = $this->factoryRecommendationService->getRecommendations(
                $latitude,
                $longitude,
                9,
                100
            );
            $recommendations['factories'] = $factoryRecommendations->take(9)->values();
        }

        return $recommendations;
    }

    /**
     * Get optimized sourcing options (best price-quality-location combination).
     */
    public function getOptimizedSourcing(
        array $items,
        float $latitude,
        float $longitude
    ): array {
        $optimizedOptions = [];

        foreach ($items as $item) {
            $itemName = strtolower($item['name'] ?? '');
            $quantity = $item['quantity'] ?? 0;
            $unit = $item['unit'] ?? '';

            // Find all matching products from stores
            $storeProducts = StoreProduct::where('is_active', true)
                ->where(function($query) use ($itemName) {
                    $query->where('name', 'like', "%{$itemName}%")
                        ->orWhere('description', 'like', "%{$itemName}%");
                })
                ->where('unit', $unit)
                ->with('store', 'store.locations')
                ->get();

            // Find all matching products from factories
            $factoryProducts = FactoryProduct::where('is_available', true)
                ->where(function($query) use ($itemName) {
                    $query->where('name', 'like', "%{$itemName}%")
                        ->orWhere('description', 'like', "%{$itemName}%");
                })
                ->where('unit', $unit)
                ->with('factory', 'factory.locations', 'factory.factoryType')
                ->get();

            $options = [];

            // Process store products
            foreach ($storeProducts as $product) {
                $store = $product->store;
                $deliveryCost = $this->calculateStoreDeliveryCost($store, $latitude, $longitude, $quantity);
                $totalCost = ($product->final_price * $quantity) + $deliveryCost;

                // Calculate quality score (based on store rating)
                $qualityScore = $store->rating ?? 0;

                $options[] = [
                    'type' => 'store',
                    'product' => $product,
                    'source' => $store,
                    'unit_price' => $product->final_price,
                    'quantity' => $quantity,
                    'subtotal' => $product->final_price * $quantity,
                    'delivery_cost' => $deliveryCost,
                    'total_cost' => $totalCost,
                    'quality_score' => $qualityScore,
                    'rating' => $store->rating ?? 0,
                ];
            }

            // Process factory products
            foreach ($factoryProducts as $product) {
                $factory = $product->factory;
                $deliveryCost = $this->calculateFactoryDeliveryCost($factory, $latitude, $longitude, $quantity);
                $totalCost = ($product->final_price * $quantity) + $deliveryCost;

                // Calculate quality score (based on factory rating and quality grade)
                $qualityScore = $factory->rating ?? 0;
                if ($product->quality_grade && is_array($product->quality_grade)) {
                    $qualityScore += count($product->quality_grade) * 0.5;
                }

                $options[] = [
                    'type' => 'factory',
                    'product' => $product,
                    'source' => $factory,
                    'unit_price' => $product->final_price,
                    'quantity' => $quantity,
                    'subtotal' => $product->final_price * $quantity,
                    'delivery_cost' => $deliveryCost,
                    'total_cost' => $totalCost,
                    'quality_score' => $qualityScore,
                    'rating' => $factory->rating ?? 0,
                    'factory_type' => $factory->factoryType?->name,
                ];
            }

            // Sort by best combination (price + quality + distance)
            usort($options, function($a, $b) {
                // Calculate combined score (lower total cost + higher quality = better)
                $scoreA = $a['total_cost'] - ($a['quality_score'] * 1000);
                $scoreB = $b['total_cost'] - ($b['quality_score'] * 1000);
                return $scoreA <=> $scoreB;
            });

            $optimizedOptions[] = [
                'item_name' => $item['name'],
                'quantity' => $quantity,
                'unit' => $unit,
                'best_option' => $options[0] ?? null,
                'all_options' => array_slice($options, 0, 5), // Top 5 options
            ];
        }

        return $optimizedOptions;
    }
}

