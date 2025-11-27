<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Helpers\GeolocationHelper;
use App\Models\Store;
use App\Models\StoreLocation;
use App\Services\StoreRecommendationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeolocationController extends Controller
{
    /**
     * Display nearest store finder page.
     */
    public function findNearest(Request $request): View
    {
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $radius = $request->get('radius', 10); // Default 10 km
        $stores = collect();

        if ($latitude && $longitude) {
            // Get all active stores with locations
            $allStores = Store::where('status', 'approved')
                ->where('is_active', true)
                ->where('is_verified', true)
                ->with(['locations' => function($query) {
                    $query->where('is_active', true);
                }])
                ->get();

            // Calculate distances and filter by radius
            $stores = $allStores->map(function($store) use ($latitude, $longitude) {
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

                if ($nearestLocation) {
                    $store->distance = $nearestLocation['distance'];
                    $store->nearest_location = $nearestLocation['location'];
                    return $store;
                }
                return null;
            })
            ->filter(function($store) use ($radius) {
                return $store && $store->distance <= $radius;
            })
            ->sortBy('distance')
            ->values();
        }

        return view('stores.find-nearest', compact('stores', 'latitude', 'longitude', 'radius'));
    }

    /**
     * Search stores by location (API endpoint).
     */
    public function search(Request $request)
    {
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:1', 'max:100'],
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->get('radius', 10);

        $allStores = Store::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['locations' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        $results = $allStores->map(function($store) use ($latitude, $longitude) {
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

            if ($nearestLocation) {
                return [
                    'uuid' => $store->uuid,
                    'name' => $store->name,
                    'slug' => $store->slug,
                    'logo' => $store->logo,
                    'rating' => $store->rating,
                    'total_reviews' => $store->total_reviews,
                    'distance' => round($nearestLocation['distance'], 2),
                    'location' => [
                        'name' => $nearestLocation['location']->name,
                        'address' => $nearestLocation['location']->full_address,
                        'latitude' => $nearestLocation['location']->latitude,
                        'longitude' => $nearestLocation['location']->longitude,
                    ],
                ];
            }
            return null;
        })
        ->filter(function($store) use ($radius) {
            return $store && $store['distance'] <= $radius;
        })
        ->sortBy('distance')
        ->values();

        return response()->json([
            'success' => true,
            'stores' => $results,
            'count' => $results->count(),
        ]);
    }

    /**
     * Get location-based store recommendations (API endpoint).
     */
    public function recommendations(Request $request)
    {
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'max_distance' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'product_name' => ['nullable', 'string', 'max:255'],
        ]);

        $recommendationService = new StoreRecommendationService();
        
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $limit = $request->get('limit', 10);
        $maxDistance = $request->get('max_distance', 50);

        // Product-based recommendations if product_name is provided
        if ($request->filled('product_name')) {
            $recommendations = $recommendationService->getProductBasedRecommendations(
                $latitude,
                $longitude,
                $request->product_name,
                $limit
            );
        } else {
            // General location-based recommendations
            $recommendations = $recommendationService->getRecommendations(
                $latitude,
                $longitude,
                $limit,
                $maxDistance
            );
        }

        $results = $recommendations->map(function($item) {
            return [
                'uuid' => $item['store']->uuid,
                'name' => $item['store']->name,
                'slug' => $item['store']->slug,
                'logo' => $item['store']->logo,
                'rating' => $item['store']->rating,
                'total_reviews' => $item['store']->total_reviews,
                'distance' => $item['distance'],
                'recommendation_score' => $item['recommendation_score'],
                'location' => [
                    'name' => $item['nearest_location']->name,
                    'address' => $item['nearest_location']->full_address,
                    'latitude' => $item['nearest_location']->latitude,
                    'longitude' => $item['nearest_location']->longitude,
                ],
                'matching_products_count' => $item['matching_products_count'] ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'recommendations' => $results,
            'count' => $results->count(),
        ]);
    }
}
