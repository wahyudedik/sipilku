<?php

namespace App\Services;

use App\Helpers\GeolocationHelper;
use App\Models\Store;
use Illuminate\Support\Collection;

class StoreRecommendationService
{
    /**
     * Get location-based store recommendations.
     * 
     * Algorithm considers:
     * - Distance (closer = better, max 50km)
     * - Rating (higher = better)
     * - Total reviews (more reviews = more reliable)
     * - Product availability (more products = better)
     * - Store activity (total orders = popularity indicator)
     * 
     * @param float $latitude
     * @param float $longitude
     * @param int $limit
     * @param float $maxDistance Maximum distance in km (default 50km)
     * @param array $excludeStoreIds Store UUIDs to exclude from recommendations
     * @return Collection
     */
    public function getRecommendations(
        float $latitude,
        float $longitude,
        int $limit = 10,
        float $maxDistance = 50,
        array $excludeStoreIds = []
    ): Collection {
        // Get all active and verified stores
        $stores = Store::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereNotIn('uuid', $excludeStoreIds)
            ->with(['locations' => function($query) {
                $query->where('is_active', true);
            }])
            ->withCount(['products' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        // Calculate recommendation score for each store
        $recommendations = $stores->map(function($store) use ($latitude, $longitude, $maxDistance) {
            // Find nearest location
            $nearestLocation = $store->locations
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

            // Calculate recommendation score
            $score = $this->calculateRecommendationScore(
                $store,
                $nearestLocation['distance'],
                $nearestLocation['location']
            );

            return [
                'store' => $store,
                'distance' => round($nearestLocation['distance'], 2),
                'nearest_location' => $nearestLocation['location'],
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
     * Calculate recommendation score for a store.
     * 
     * Score components (0-100):
     * - Distance score: 40% (closer = higher score)
     * - Rating score: 30% (higher rating = higher score)
     * - Reviews score: 15% (more reviews = higher score, but diminishing returns)
     * - Products score: 10% (more products = higher score)
     * - Activity score: 5% (more orders = higher score)
     * 
     * @param Store $store
     * @param float $distance Distance in km
     * @param mixed $location Nearest store location
     * @return float
     */
    private function calculateRecommendationScore(Store $store, float $distance, $location): float
    {
        // Distance score (40% weight) - closer is better
        // Max distance considered: 50km
        // Score: 100 at 0km, 0 at 50km
        $distanceScore = max(0, 100 - ($distance / 50 * 100));
        $distanceWeight = 0.40;

        // Rating score (30% weight) - 0-5 stars
        // Score: 0 at 0 stars, 100 at 5 stars
        // Rating is stored as integer (1-5), but we treat it as 0-5 scale
        $rating = $store->rating ?? 0;
        $ratingScore = ($rating / 5) * 100;
        $ratingWeight = 0.30;

        // Reviews score (15% weight) - more reviews = more reliable
        // Score: 0 at 0 reviews, 100 at 100+ reviews (diminishing returns)
        $reviewsScore = min(100, ($store->total_reviews / 100) * 100);
        $reviewsWeight = 0.15;

        // Products score (10% weight) - more products = better selection
        // Score: 0 at 0 products, 100 at 100+ products
        $productsCount = $store->products_count ?? $store->products()->where('is_active', true)->count();
        $productsScore = min(100, ($productsCount / 100) * 100);
        $productsWeight = 0.10;

        // Activity score (5% weight) - more orders = more popular
        // Score: 0 at 0 orders, 100 at 500+ orders
        $activityScore = min(100, ($store->total_orders / 500) * 100);
        $activityWeight = 0.05;

        // Calculate weighted total score
        $totalScore = (
            ($distanceScore * $distanceWeight) +
            ($ratingScore * $ratingWeight) +
            ($reviewsScore * $reviewsWeight) +
            ($productsScore * $productsWeight) +
            ($activityScore * $activityWeight)
        );

        return round($totalScore, 2);
    }

    /**
     * Get recommendations based on a reference store.
     * Recommends similar stores near the reference store.
     * 
     * @param Store $referenceStore
     * @param int $limit
     * @return Collection
     */
    public function getSimilarStoreRecommendations(Store $referenceStore, int $limit = 5): Collection
    {
        // Get primary location of reference store
        $primaryLocation = $referenceStore->primaryLocation()->where('is_active', true)->first();
        
        if (!$primaryLocation || !$primaryLocation->hasCoordinates()) {
            return collect();
        }

        return $this->getRecommendations(
            $primaryLocation->latitude,
            $primaryLocation->longitude,
            $limit,
            30, // Max 30km for similar stores
            [$referenceStore->uuid] // Exclude the reference store
        );
    }

    /**
     * Get recommendations for a specific product search.
     * Recommends stores that have the product and are nearby.
     * 
     * @param float $latitude
     * @param float $longitude
     * @param string $productName Product name to search for
     * @param int $limit
     * @return Collection
     */
    public function getProductBasedRecommendations(
        float $latitude,
        float $longitude,
        string $productName,
        int $limit = 10
    ): Collection {
        // Get stores that have products matching the search
        $stores = Store::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereHas('products', function($query) use ($productName) {
                $query->where('is_active', true)
                      ->where(function($q) use ($productName) {
                          $q->where('name', 'like', "%{$productName}%")
                            ->orWhere('description', 'like', "%{$productName}%");
                      });
            })
            ->with(['locations' => function($query) {
                $query->where('is_active', true);
            }])
            ->withCount(['products' => function($query) use ($productName) {
                $query->where('is_active', true)
                      ->where(function($q) use ($productName) {
                          $q->where('name', 'like', "%{$productName}%")
                            ->orWhere('description', 'like', "%{$productName}%");
                      });
            }])
            ->get();

        // Calculate recommendations
        $recommendations = $stores->map(function($store) use ($latitude, $longitude) {
            $nearestLocation = $store->locations
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

            if (!$nearestLocation || $nearestLocation['distance'] > 50) {
                return null;
            }

            // Boost score if store has more matching products
            $matchingProductsCount = $store->products_count ?? 0;
            $baseScore = $this->calculateRecommendationScore(
                $store,
                $nearestLocation['distance'],
                $nearestLocation['location']
            );
            
            // Add bonus for product match (up to 10 points)
            $productBonus = min(10, ($matchingProductsCount / 5) * 10);
            $score = $baseScore + $productBonus;

            return [
                'store' => $store,
                'distance' => round($nearestLocation['distance'], 2),
                'nearest_location' => $nearestLocation['location'],
                'recommendation_score' => round($score, 2),
                'matching_products_count' => $matchingProductsCount,
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
}

