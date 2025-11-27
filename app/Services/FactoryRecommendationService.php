<?php

namespace App\Services;

use App\Helpers\GeolocationHelper;
use App\Models\Factory;
use App\Models\FactoryType;
use Illuminate\Support\Collection;

class FactoryRecommendationService
{
    /**
     * Get location-based factory recommendations for all factory types.
     * 
     * Algorithm considers:
     * - Distance (closer = better, max 100km for factories)
     * - Rating (higher = better)
     * - Quality (from reviews and certifications)
     * - Availability (capacity and stock)
     * - Total cost (product price + delivery cost)
     * 
     * @param float $latitude
     * @param float $longitude
     * @param int $limit
     * @param float $maxDistance Maximum distance in km (default 100km)
     * @param string|null $factoryTypeId Filter by factory type UUID
     * @param array $excludeFactoryIds Factory UUIDs to exclude
     * @param float|null $productPrice Optional product price for total cost calculation
     * @param float|null $quantity Optional quantity for total cost calculation
     * @return Collection
     */
    public function getRecommendations(
        float $latitude,
        float $longitude,
        int $limit = 10,
        float $maxDistance = 100,
        ?string $factoryTypeId = null,
        array $excludeFactoryIds = [],
        ?float $productPrice = null,
        ?float $quantity = null
    ): Collection {
        // Build query
        $query = Factory::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereNotIn('uuid', $excludeFactoryIds);

        // Filter by factory type if specified
        if ($factoryTypeId) {
            $query->where('factory_type_id', $factoryTypeId);
        }

        $factories = $query->with(['locations' => function($query) {
                $query->where('is_active', true);
            }])
            ->with(['factoryType'])
            ->withCount(['products' => function($query) {
                $query->where('is_available', true);
            }])
            ->get();

        // Calculate recommendation score for each factory
        $recommendations = $factories->map(function($factory) use ($latitude, $longitude, $maxDistance, $productPrice, $quantity) {
            // Find nearest location
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

            if (!$nearestLocation || $nearestLocation['distance'] > $maxDistance) {
                return null;
            }

            // Calculate delivery cost
            $deliveryCost = 0;
            if ($factory->delivery_price_per_km) {
                $deliveryCost = GeolocationHelper::calculateDeliveryCost(
                    $nearestLocation['distance'],
                    $factory->delivery_price_per_km
                );
            }

            // Calculate total cost if product price and quantity provided
            $totalCost = null;
            if ($productPrice !== null && $quantity !== null) {
                $totalCost = ($productPrice * $quantity) + $deliveryCost;
            }

            // Calculate recommendation score
            $score = $this->calculateRecommendationScore(
                $factory,
                $nearestLocation['distance'],
                $nearestLocation['location'],
                $deliveryCost,
                $totalCost
            );

            return [
                'factory' => $factory,
                'distance' => round($nearestLocation['distance'], 2),
                'nearest_location' => $nearestLocation['location'],
                'delivery_cost' => round($deliveryCost, 2),
                'total_cost' => $totalCost ? round($totalCost, 2) : null,
                'recommendation_score' => $score,
            ];
        })
        ->filter(function($item) {
            return $item !== null;
        })
        ->sortByDesc('recommendation_score')
        ->take($limit)
        ->values();

        return $recommendations;
    }

    /**
     * Calculate recommendation score for a factory.
     * 
     * Score components (0-100):
     * - Distance score: 30% (closer = higher score)
     * - Rating score: 25% (higher rating = higher score)
     * - Quality score: 20% (certifications, reviews quality)
     * - Availability score: 15% (capacity, stock availability)
     * - Cost score: 10% (lower total cost = higher score, only if cost data available)
     * 
     * @param Factory $factory
     * @param float $distance Distance in km
     * @param mixed $location Nearest factory location
     * @param float $deliveryCost Delivery cost
     * @param float|null $totalCost Total cost (product + delivery)
     * @return float
     */
    private function calculateRecommendationScore(
        Factory $factory,
        float $distance,
        $location,
        float $deliveryCost,
        ?float $totalCost
    ): float {
        // Distance score (30% weight) - closer is better
        // Max distance considered: 100km
        // Score: 100 at 0km, 0 at 100km
        $distanceScore = max(0, 100 - ($distance / 100 * 100));
        $distanceWeight = 0.30;

        // Rating score (25% weight) - 0-5 stars
        $rating = $factory->rating ?? 0;
        $ratingScore = ($rating / 5) * 100;
        $ratingWeight = 0.25;

        // Quality score (20% weight) - based on certifications and reviews
        $qualityScore = $this->calculateQualityScore($factory);
        $qualityWeight = 0.20;

        // Availability score (15% weight) - based on capacity and product availability
        $availabilityScore = $this->calculateAvailabilityScore($factory);
        $availabilityWeight = 0.15;

        // Cost score (10% weight) - lower cost = higher score
        // Only calculated if total cost is available
        $costScore = 0;
        $costWeight = 0.10;
        if ($totalCost !== null) {
            // Normalize cost score (assume max reasonable cost is 10M, score decreases as cost increases)
            // Score: 100 at 0 cost, 0 at 10M cost
            $costScore = max(0, 100 - (($totalCost / 10000000) * 100));
        } else {
            // If no cost data, redistribute weight to other factors
            $distanceWeight = 0.35;
            $ratingWeight = 0.30;
            $qualityWeight = 0.20;
            $availabilityWeight = 0.15;
        }

        // Calculate weighted total score
        $totalScore = (
            ($distanceScore * $distanceWeight) +
            ($ratingScore * $ratingWeight) +
            ($qualityScore * $qualityWeight) +
            ($availabilityScore * $availabilityWeight) +
            ($costScore * $costWeight)
        );

        return round($totalScore, 2);
    }

    /**
     * Calculate quality score based on certifications and reviews.
     * 
     * @param Factory $factory
     * @return float
     */
    private function calculateQualityScore(Factory $factory): float
    {
        $score = 50; // Base score

        // Certifications boost (up to 30 points)
        $certifications = $factory->certifications ?? [];
        if (is_array($certifications) && count($certifications) > 0) {
            $certScore = min(30, count($certifications) * 10);
            $score += $certScore;
        }

        // Reviews quality boost (up to 20 points)
        // More reviews with high ratings = better quality indicator
        if ($factory->total_reviews > 0) {
            $reviewScore = min(20, ($factory->total_reviews / 50) * 20);
            $score += $reviewScore;
        }

        return min(100, $score);
    }

    /**
     * Calculate availability score based on capacity and product availability.
     * 
     * @param Factory $factory
     * @return float
     */
    private function calculateAvailabilityScore(Factory $factory): float
    {
        $score = 50; // Base score

        // Product availability boost (up to 30 points)
        $productsCount = $factory->products_count ?? $factory->products()->where('is_available', true)->count();
        if ($productsCount > 0) {
            $productScore = min(30, ($productsCount / 20) * 30);
            $score += $productScore;
        }

        // Capacity boost (up to 20 points)
        $capacity = $factory->capacity ?? [];
        if (is_array($capacity) && !empty($capacity)) {
            $score += 20;
        }

        return min(100, $score);
    }

    /**
     * Get factory type-specific recommendations.
     * 
     * @param float $latitude
     * @param float $longitude
     * @param string $factoryTypeSlug Factory type slug (e.g., 'beton', 'bata', 'genting')
     * @param int $limit
     * @return Collection
     */
    public function getTypeSpecificRecommendations(
        float $latitude,
        float $longitude,
        string $factoryTypeSlug,
        int $limit = 10
    ): Collection {
        $factoryType = FactoryType::where('slug', $factoryTypeSlug)
            ->where('is_active', true)
            ->first();

        if (!$factoryType) {
            return collect();
        }

        return $this->getRecommendations(
            $latitude,
            $longitude,
            $limit,
            100,
            $factoryType->uuid
        );
    }

    /**
     * Get recommendations for contractors based on project location.
     * 
     * @param float $latitude Project location latitude
     * @param float $longitude Project location longitude
     * @param string|null $factoryTypeId Optional factory type filter
     * @param int $limit
     * @return Collection
     */
    public function getContractorRecommendations(
        float $latitude,
        float $longitude,
        ?string $factoryTypeId = null,
        int $limit = 10
    ): Collection {
        return $this->getRecommendations(
            $latitude,
            $longitude,
            $limit,
            100,
            $factoryTypeId
        );
    }

    /**
     * Get smart recommendations that avoid expensive delivery costs.
     * Focuses on best quality-price ratio.
     * 
     * @param float $latitude
     * @param float $longitude
     * @param float $productPrice Product price per unit
     * @param float $quantity Quantity needed
     * @param string|null $factoryTypeId Optional factory type filter
     * @param int $limit
     * @return Collection
     */
    public function getSmartRecommendations(
        float $latitude,
        float $longitude,
        float $productPrice,
        float $quantity,
        ?string $factoryTypeId = null,
        int $limit = 10
    ): Collection {
        // Get recommendations with cost calculation
        $recommendations = $this->getRecommendations(
            $latitude,
            $longitude,
            $limit * 2, // Get more to filter
            100,
            $factoryTypeId,
            [],
            $productPrice,
            $quantity
        );

        // Filter out factories with very expensive delivery (more than 30% of product cost)
        $maxDeliveryRatio = 0.30;
        $productTotal = $productPrice * $quantity;

        $filtered = $recommendations->filter(function($item) use ($maxDeliveryRatio, $productTotal) {
            if ($item['total_cost'] === null) {
                return true; // Keep if no cost data
            }
            
            $deliveryRatio = $item['delivery_cost'] / $productTotal;
            return $deliveryRatio <= $maxDeliveryRatio;
        });

        // Re-sort by recommendation score (which includes cost factor)
        return $filtered->sortByDesc('recommendation_score')
            ->take($limit)
            ->values();
    }
}

