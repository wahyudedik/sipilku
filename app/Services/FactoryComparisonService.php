<?php

namespace App\Services;

use App\Helpers\GeolocationHelper;
use App\Models\Factory;
use App\Models\FactoryProduct;
use Illuminate\Support\Collection;

class FactoryComparisonService
{
    /**
     * Compare total cost (product price + delivery) across factories.
     * 
     * @param string $productName Product name to search for
     * @param float $latitude Destination latitude
     * @param float $longitude Destination longitude
     * @param float $quantity Quantity needed
     * @param string|null $factoryTypeId Optional factory type filter
     * @param array $factoryIds Optional: specific factory UUIDs to compare
     * @return Collection
     */
    public function compareTotalCost(
        string $productName,
        float $latitude,
        float $longitude,
        float $quantity,
        ?string $factoryTypeId = null,
        array $factoryIds = []
    ): Collection {
        $query = Factory::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereHas('products', function($q) use ($productName) {
                $q->where('is_available', true)
                  ->where(function($query) use ($productName) {
                      $query->where('name', 'like', "%{$productName}%")
                            ->orWhere('description', 'like', "%{$productName}%");
                  });
            })
            ->with(['products' => function($q) use ($productName) {
                $q->where('is_available', true)
                  ->where(function($query) use ($productName) {
                      $query->where('name', 'like', "%{$productName}%")
                            ->orWhere('description', 'like', "%{$productName}%");
                  })
                  ->orderByRaw('COALESCE(discount_price, price) ASC');
            }])
            ->with(['locations' => function($q) {
                $q->where('is_active', true);
            }])
            ->with(['factoryType']);

        if ($factoryTypeId) {
            $query->where('factory_type_id', $factoryTypeId);
        }

        if (!empty($factoryIds)) {
            $query->whereIn('uuid', $factoryIds);
        }

        $factories = $query->get();

        return $factories->map(function($factory) use ($latitude, $longitude, $quantity) {
            $products = $factory->products;
            $cheapestProduct = $products->first();
            
            if (!$cheapestProduct) {
                return null;
            }

            $productPrice = $cheapestProduct->final_price ?? $cheapestProduct->price;
            $productTotal = $productPrice * $quantity;

            // Find nearest location and calculate delivery cost
            $nearestLocation = $factory->locations
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
                        'distance' => $distance
                    ];
                })
                ->sortBy('distance')
                ->first();

            $deliveryCost = 0;
            $distance = null;
            if ($nearestLocation && $factory->delivery_price_per_km) {
                $distance = $nearestLocation['distance'];
                $deliveryCost = GeolocationHelper::calculateDeliveryCost(
                    $distance,
                    $factory->delivery_price_per_km
                );
            }

            $totalCost = $productTotal + $deliveryCost;

            return [
                'factory' => $factory,
                'product' => $cheapestProduct,
                'product_price' => $productPrice,
                'product_total' => round($productTotal, 2),
                'delivery_cost' => round($deliveryCost, 2),
                'total_cost' => round($totalCost, 2),
                'distance' => $distance ? round($distance, 2) : null,
                'location' => $nearestLocation ? $nearestLocation['location'] : null,
            ];
        })
        ->filter(function($item) {
            return $item !== null;
        })
        ->sortBy('total_cost')
        ->values();
    }

    /**
     * Compare quality across factories for the same product type.
     * 
     * @param string $productName Product name
     * @param string|null $factoryTypeId Optional factory type filter
     * @param array $factoryIds Optional: specific factory UUIDs to compare
     * @return Collection
     */
    public function compareQuality(
        string $productName,
        ?string $factoryTypeId = null,
        array $factoryIds = []
    ): Collection {
        $query = Factory::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereHas('products', function($q) use ($productName) {
                $q->where('is_available', true)
                  ->where(function($query) use ($productName) {
                      $query->where('name', 'like', "%{$productName}%")
                            ->orWhere('description', 'like', "%{$productName}%");
                  });
            })
            ->with(['products' => function($q) use ($productName) {
                $q->where('is_available', true)
                  ->where(function($query) use ($productName) {
                      $query->where('name', 'like', "%{$productName}%")
                            ->orWhere('description', 'like', "%{$productName}%");
                  });
            }])
            ->with(['factoryType'])
            ->withCount(['approvedReviews']);

        if ($factoryTypeId) {
            $query->where('factory_type_id', $factoryTypeId);
        }

        if (!empty($factoryIds)) {
            $query->whereIn('uuid', $factoryIds);
        }

        $factories = $query->get();

        return $factories->map(function($factory) {
            // Calculate quality score
            $qualityScore = $this->calculateQualityScore($factory);

            return [
                'factory' => $factory,
                'rating' => $factory->rating ?? 0,
                'total_reviews' => $factory->approved_reviews_count ?? 0,
                'certifications' => $factory->certifications ?? [],
                'certification_count' => is_array($factory->certifications) ? count($factory->certifications) : 0,
                'quality_score' => $qualityScore,
                'products' => $factory->products,
            ];
        })
        ->sortByDesc('quality_score')
        ->values();
    }

    /**
     * Compare factories by multiple criteria (delivery cost, quality, rating).
     * 
     * @param array $factoryIds Factory UUIDs to compare
     * @param float|null $latitude Optional: for delivery cost calculation
     * @param float|null $longitude Optional: for delivery cost calculation
     * @return Collection
     */
    public function compareFactories(
        array $factoryIds,
        ?float $latitude = null,
        ?float $longitude = null
    ): Collection {
        $factories = Factory::whereIn('uuid', $factoryIds)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['factoryType'])
            ->with(['locations' => function($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['products' => function($q) {
                $q->where('is_available', true);
            }])
            ->withCount(['approvedReviews'])
            ->get();

        return $factories->map(function($factory) use ($latitude, $longitude) {
            $deliveryCost = null;
            $distance = null;
            $nearestLocation = null;

            // Calculate delivery cost if coordinates provided
            if ($latitude !== null && $longitude !== null) {
                $nearestLocation = $factory->locations
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
                            'distance' => $distance
                        ];
                    })
                    ->sortBy('distance')
                    ->first();

                if ($nearestLocation && $factory->delivery_price_per_km) {
                    $distance = $nearestLocation['distance'];
                    $deliveryCost = GeolocationHelper::calculateDeliveryCost(
                        $distance,
                        $factory->delivery_price_per_km
                    );
                }
            }

            $qualityScore = $this->calculateQualityScore($factory);

            return [
                'factory' => $factory,
                'rating' => $factory->rating ?? 0,
                'total_reviews' => $factory->approved_reviews_count ?? 0,
                'product_count' => $factory->products_count ?? 0,
                'certifications' => $factory->certifications ?? [],
                'certification_count' => is_array($factory->certifications) ? count($factory->certifications) : 0,
                'quality_score' => $qualityScore,
                'delivery_price_per_km' => $factory->delivery_price_per_km,
                'max_delivery_distance' => $factory->max_delivery_distance,
                'delivery_cost' => $deliveryCost ? round($deliveryCost, 2) : null,
                'distance' => $distance ? round($distance, 2) : null,
                'location' => $nearestLocation ? $nearestLocation['location'] : null,
            ];
        })
        ->values();
    }

    /**
     * Calculate quality score for a factory.
     * 
     * @param Factory $factory
     * @return float
     */
    private function calculateQualityScore(Factory $factory): float
    {
        $score = 0;

        // Rating component (0-50 points)
        $rating = $factory->rating ?? 0;
        $score += ($rating / 5) * 50;

        // Certifications component (0-30 points)
        $certifications = $factory->certifications ?? [];
        if (is_array($certifications) && count($certifications) > 0) {
            $score += min(30, count($certifications) * 10);
        }

        // Reviews component (0-20 points)
        $reviewsCount = $factory->approved_reviews_count ?? 0;
        if ($reviewsCount > 0) {
            $score += min(20, ($reviewsCount / 50) * 20);
        }

        return round($score, 2);
    }
}

