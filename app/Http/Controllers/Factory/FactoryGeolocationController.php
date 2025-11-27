<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Helpers\GeolocationHelper;
use App\Models\Factory;
use App\Models\FactoryType;
use App\Services\FactoryRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FactoryGeolocationController extends Controller
{
    /**
     * Display nearest factory finder page with Google Maps.
     */
    public function findNearest(Request $request): View
    {
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $radius = $request->get('radius', 50); // Default 50 km for factories
        $factoryTypeId = $request->get('factory_type_id');
        $factoryTypeSlug = $request->get('factory_type');
        $factories = collect();

        // Get factory types for filter
        $factoryTypes = FactoryType::where('is_active', true)->orderBy('name')->get();

        if ($latitude && $longitude) {
            // Build query
            $query = Factory::where('status', 'approved')
                ->where('is_active', true)
                ->where('is_verified', true)
                ->with(['locations' => function($q) {
                    $q->where('is_active', true);
                }, 'factoryType']);

            // Filter by factory type
            if ($factoryTypeId) {
                $query->where('factory_type_id', $factoryTypeId);
            } elseif ($factoryTypeSlug) {
                $query->whereHas('factoryType', function($q) use ($factoryTypeSlug) {
                    $q->where('slug', $factoryTypeSlug);
                });
            }

            $allFactories = $query->get();

            // Calculate distances and filter by radius
            $factories = $allFactories->map(function($factory) use ($latitude, $longitude) {
                $nearestLocation = $factory->locations
                    ->filter(function($location) {
                        return $location->hasCoordinates();
                    })
                    ->map(function($location) use ($latitude, $longitude, $factory) {
                        $distance = GeolocationHelper::calculateDistance(
                            $latitude,
                            $longitude,
                            $location->latitude,
                            $location->longitude
                        );
                        
                        // Calculate delivery cost
                        $deliveryCost = 0;
                        if ($factory->delivery_price_per_km) {
                            $deliveryCost = GeolocationHelper::calculateDeliveryCost(
                                $distance,
                                $factory->delivery_price_per_km
                            );
                        }

                        return [
                            'location' => $location,
                            'distance' => $distance,
                            'delivery_cost' => $deliveryCost,
                        ];
                    })
                    ->sortBy('distance')
                    ->first();

                if ($nearestLocation) {
                    $factory->distance = $nearestLocation['distance'];
                    $factory->delivery_cost = $nearestLocation['delivery_cost'];
                    $factory->nearest_location = $nearestLocation['location'];
                    return $factory;
                }
                return null;
            })
            ->filter(function($factory) use ($radius) {
                return $factory && $factory->distance <= $radius;
            })
            ->sortBy('distance')
            ->values();
        }

        return view('factories.find-nearest', compact('factories', 'latitude', 'longitude', 'radius', 'factoryTypes', 'factoryTypeId', 'factoryTypeSlug'));
    }

    /**
     * Search factories by location (API endpoint).
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:1', 'max:200'],
            'factory_type_id' => ['nullable', 'string', 'exists:factory_types,uuid'],
            'factory_type_slug' => ['nullable', 'string', 'exists:factory_types,slug'],
            'factory_type_slugs' => ['nullable', 'array'], // Multiple factory types
            'factory_type_slugs.*' => ['string', 'exists:factory_types,slug'],
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->get('radius', 50);

        // Build query
        $query = Factory::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['locations' => function($q) {
                $q->where('is_active', true);
            }, 'factoryType']);

        // Filter by single factory type ID
        if ($request->filled('factory_type_id')) {
            $query->where('factory_type_id', $request->factory_type_id);
        }
        // Filter by single factory type slug
        elseif ($request->filled('factory_type_slug')) {
            $query->whereHas('factoryType', function($q) use ($request) {
                $q->where('slug', $request->factory_type_slug);
            });
        }
        // Filter by multiple factory type slugs
        elseif ($request->filled('factory_type_slugs') && is_array($request->factory_type_slugs)) {
            $query->whereHas('factoryType', function($q) use ($request) {
                $q->whereIn('slug', $request->factory_type_slugs);
            });
        }

        $allFactories = $query->get();

        $results = $allFactories->map(function($factory) use ($latitude, $longitude) {
            $nearestLocation = $factory->locations
                ->filter(function($location) {
                    return $location->hasCoordinates();
                })
                ->map(function($location) use ($latitude, $longitude, $factory) {
                    $distance = GeolocationHelper::calculateDistance(
                        $latitude,
                        $longitude,
                        $location->latitude,
                        $location->longitude
                    );
                    
                    $deliveryCost = 0;
                    if ($factory->delivery_price_per_km) {
                        $deliveryCost = GeolocationHelper::calculateDeliveryCost(
                            $distance,
                            $factory->delivery_price_per_km
                        );
                    }

                    return [
                        'location' => $location,
                        'distance' => $distance,
                        'delivery_cost' => $deliveryCost,
                    ];
                })
                ->sortBy('distance')
                ->first();

            if ($nearestLocation) {
                return [
                    'uuid' => $factory->uuid,
                    'name' => $factory->name,
                    'slug' => $factory->slug,
                    'logo' => $factory->logo,
                    'factory_type' => $factory->factoryType ? [
                        'name' => $factory->factoryType->name,
                        'slug' => $factory->factoryType->slug,
                    ] : null,
                    'rating' => $factory->rating,
                    'total_reviews' => $factory->total_reviews,
                    'delivery_price_per_km' => $factory->delivery_price_per_km,
                    'max_delivery_distance' => $factory->max_delivery_distance,
                    'distance' => round($nearestLocation['distance'], 2),
                    'delivery_cost' => round($nearestLocation['delivery_cost'], 2),
                    'location' => [
                        'name' => $nearestLocation['location']->name,
                        'address' => $nearestLocation['location']->full_address,
                        'latitude' => $nearestLocation['location']->latitude,
                        'longitude' => $nearestLocation['location']->longitude,
                        'operating_hours' => $nearestLocation['location']->operating_hours,
                    ],
                ];
            }
            return null;
        })
        ->filter(function($factory) use ($radius) {
            return $factory && $factory['distance'] <= $radius;
        })
        ->sortBy('distance')
        ->values();

        return response()->json([
            'success' => true,
            'factories' => $results,
            'count' => $results->count(),
        ]);
    }

    /**
     * Display factory location map.
     */
    public function map(Request $request): View
    {
        $factoryTypeId = $request->get('factory_type_id');
        $factoryTypeSlug = $request->get('factory_type');
        $latitude = $request->get('latitude', -6.2088); // Default to Jakarta
        $longitude = $request->get('longitude', 106.8456);
        $zoom = $request->get('zoom', 10);

        // Build query
        $query = Factory::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['locations' => function($q) {
                $q->where('is_active', true);
            }, 'factoryType']);

        // Filter by factory type
        if ($factoryTypeId) {
            $query->where('factory_type_id', $factoryTypeId);
        } elseif ($factoryTypeSlug) {
            $query->whereHas('factoryType', function($q) use ($factoryTypeSlug) {
                $q->where('slug', $factoryTypeSlug);
            });
        }

        $factories = $query->get()
            ->map(function($factory) {
                $primaryLocation = $factory->locations
                    ->where('is_primary', true)
                    ->first() ?? $factory->locations->first();

                if ($primaryLocation && $primaryLocation->hasCoordinates()) {
                    return [
                        'uuid' => $factory->uuid,
                        'name' => $factory->name,
                        'slug' => $factory->slug,
                        'logo' => $factory->logo,
                        'factory_type' => $factory->factoryType ? [
                            'name' => $factory->factoryType->name,
                            'slug' => $factory->factoryType->slug,
                        ] : null,
                        'latitude' => $primaryLocation->latitude,
                        'longitude' => $primaryLocation->longitude,
                        'address' => $primaryLocation->full_address,
                        'operating_hours' => $primaryLocation->operating_hours,
                    ];
                }
                return null;
            })
            ->filter()
            ->values();

        $factoryTypes = FactoryType::where('is_active', true)->orderBy('name')->get();

        return view('factories.map', compact('factories', 'factoryTypes', 'latitude', 'longitude', 'zoom', 'factoryTypeId', 'factoryTypeSlug'));
    }

    /**
     * Calculate delivery cost with product type variations.
     */
    public function calculateDeliveryCost(Request $request): JsonResponse
    {
        $request->validate([
            'factory_id' => ['required', 'string', 'exists:factories,uuid'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'product_type' => ['nullable', 'string'], // beton, bata, genting, baja, precast, keramik, kayu
            'quantity' => ['nullable', 'numeric', 'min:0.01'],
            'unit' => ['nullable', 'string'], // m3, m2, kg, pcs, etc.
        ]);

        $factory = Factory::where('uuid', $request->factory_id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['locations' => function($q) {
                $q->where('is_active', true);
            }])
            ->firstOrFail();

        // Find nearest location
        $nearestLocation = $factory->locations
            ->filter(function($location) {
                return $location->hasCoordinates();
            })
            ->map(function($location) use ($request) {
                $distance = GeolocationHelper::calculateDistance(
                    $request->latitude,
                    $request->longitude,
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
            return response()->json([
                'success' => false,
                'message' => 'Factory location not found or coordinates missing.',
            ], 404);
        }

        $distance = $nearestLocation['distance'];
        $basePricePerKm = $factory->delivery_price_per_km ?? 0;

        // Calculate base delivery cost
        $baseDeliveryCost = GeolocationHelper::calculateDeliveryCost($distance, $basePricePerKm);

        // Apply product type multipliers
        $multiplier = $this->getProductTypeMultiplier($request->product_type, $request->unit);
        $deliveryCost = $baseDeliveryCost * $multiplier;

        // Check max delivery distance
        $canDeliver = true;
        if ($factory->max_delivery_distance && $distance > $factory->max_delivery_distance) {
            $canDeliver = false;
        }

        return response()->json([
            'success' => true,
            'distance' => round($distance, 2),
            'base_price_per_km' => $basePricePerKm,
            'base_delivery_cost' => round($baseDeliveryCost, 2),
            'product_type' => $request->product_type,
            'multiplier' => $multiplier,
            'delivery_cost' => round($deliveryCost, 2),
            'can_deliver' => $canDeliver,
            'max_delivery_distance' => $factory->max_delivery_distance,
            'location' => [
                'name' => $nearestLocation['location']->name,
                'address' => $nearestLocation['location']->full_address,
                'latitude' => $nearestLocation['location']->latitude,
                'longitude' => $nearestLocation['location']->longitude,
            ],
        ]);
    }

    /**
     * Get product type multiplier for delivery cost calculation.
     */
    private function getProductTypeMultiplier(?string $productType, ?string $unit): float
    {
        if (!$productType && !$unit) {
            return 1.0;
        }

        // Product type-based multipliers
        $typeMultiplier = match(strtolower($productType ?? '')) {
            'beton', 'concrete' => 1.5, // Heavier, requires special transport
            'baja', 'steel' => 1.8, // Very heavy
            'precast' => 2.0, // Heavy and large, requires crane
            'bata', 'brick' => 1.2, // Moderate weight
            'genting', 'roof-tile', 'tile' => 1.1, // Fragile, careful handling
            'keramik', 'granit', 'ceramic', 'granite' => 1.3, // Fragile, careful handling
            'kayu', 'wood' => 1.0, // Standard
            default => 1.0,
        };

        // Unit-based adjustments
        $unitMultiplier = match(strtolower($unit ?? '')) {
            'm3', 'kubik' => 1.3, // Volume-based, usually heavier
            'ton' => 1.5, // Very heavy
            'mobil' => 2.0, // Full truck load
            'm2' => 1.1, // Area-based
            'kg' => 1.0, // Weight-based, standard
            'pcs', 'unit' => 1.0, // Standard
            default => 1.0,
        };

        // Use the higher multiplier
        return max($typeMultiplier, $unitMultiplier);
    }
}

