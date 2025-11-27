<?php

namespace App\Http\Controllers;

use App\Models\ProjectLocation;
use App\Services\FactoryRecommendationService;
use App\Services\StoreRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    /**
     * Get nearest store recommendations for contractors.
     */
    public function nearestStores(Request $request): JsonResponse|View
    {
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'max_distance' => ['nullable', 'numeric', 'min:1', 'max:200'],
        ]);

        $service = new StoreRecommendationService();
        $recommendations = $service->getRecommendations(
            $request->latitude,
            $request->longitude,
            $request->get('limit', 10),
            $request->get('max_distance', 50)
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations->map(function($item) {
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
                    ];
                }),
                'count' => $recommendations->count(),
            ]);
        }

        return view('recommendations.stores', compact('recommendations'));
    }

    /**
     * Get nearest factory recommendations for contractors (all types).
     */
    public function nearestFactories(Request $request): JsonResponse|View
    {
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'factory_type_id' => ['nullable', 'string', 'exists:factory_types,uuid'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'max_distance' => ['nullable', 'numeric', 'min:1', 'max:200'],
        ]);

        $service = new FactoryRecommendationService();
        $recommendations = $service->getRecommendations(
            $request->latitude,
            $request->longitude,
            $request->get('limit', 10),
            $request->get('max_distance', 100),
            $request->factory_type_id
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations->map(function($item) {
                    return [
                        'uuid' => $item['factory']->uuid,
                        'name' => $item['factory']->name,
                        'slug' => $item['factory']->slug,
                        'logo' => $item['factory']->logo,
                        'factory_type' => $item['factory']->factoryType ? [
                            'name' => $item['factory']->factoryType->name,
                            'slug' => $item['factory']->factoryType->slug,
                        ] : null,
                        'rating' => $item['factory']->rating,
                        'total_reviews' => $item['factory']->total_reviews,
                        'distance' => $item['distance'],
                        'delivery_cost' => $item['delivery_cost'],
                        'total_cost' => $item['total_cost'],
                        'recommendation_score' => $item['recommendation_score'],
                        'location' => [
                            'name' => $item['nearest_location']->name,
                            'address' => $item['nearest_location']->full_address,
                            'latitude' => $item['nearest_location']->latitude,
                            'longitude' => $item['nearest_location']->longitude,
                        ],
                    ];
                }),
                'count' => $recommendations->count(),
            ]);
        }

        // If view exists, use it; otherwise return JSON
        if (view()->exists('factories.recommendations')) {
            return view('factories.recommendations', compact('recommendations'));
        }
        
        return view('recommendations.factories', compact('recommendations'));
    }

    /**
     * Get factory type-specific recommendations.
     */
    public function factoryTypeRecommendations(Request $request, string $factoryTypeSlug): JsonResponse
    {
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $service = new FactoryRecommendationService();
        $recommendations = $service->getTypeSpecificRecommendations(
            $request->latitude,
            $request->longitude,
            $factoryTypeSlug,
            $request->get('limit', 10)
        );

        return response()->json([
            'success' => true,
            'factory_type' => $factoryTypeSlug,
            'recommendations' => $recommendations->map(function($item) {
                return [
                    'uuid' => $item['factory']->uuid,
                    'name' => $item['factory']->name,
                    'slug' => $item['factory']->slug,
                    'logo' => $item['factory']->logo,
                    'rating' => $item['factory']->rating,
                    'distance' => $item['distance'],
                    'delivery_cost' => $item['delivery_cost'],
                    'recommendation_score' => $item['recommendation_score'],
                ];
            }),
            'count' => $recommendations->count(),
        ]);
    }

    /**
     * Get smart recommendations (avoid expensive delivery, best quality-price ratio).
     */
    public function smartRecommendations(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'product_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'factory_type_id' => ['nullable', 'string', 'exists:factory_types,uuid'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $service = new FactoryRecommendationService();
        $recommendations = $service->getSmartRecommendations(
            $request->latitude,
            $request->longitude,
            $request->product_price,
            $request->quantity,
            $request->factory_type_id,
            $request->get('limit', 10)
        );

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations->map(function($item) {
                return [
                    'uuid' => $item['factory']->uuid,
                    'name' => $item['factory']->name,
                    'slug' => $item['factory']->slug,
                    'logo' => $item['factory']->logo,
                    'factory_type' => $item['factory']->factoryType ? [
                        'name' => $item['factory']->factoryType->name,
                        'slug' => $item['factory']->factoryType->slug,
                    ] : null,
                    'rating' => $item['factory']->rating,
                    'distance' => $item['distance'],
                    'delivery_cost' => $item['delivery_cost'],
                    'total_cost' => $item['total_cost'],
                    'recommendation_score' => $item['recommendation_score'],
                ];
            }),
            'count' => $recommendations->count(),
        ]);
    }

    /**
     * Get recommendations for contractor based on project location.
     */
    public function contractorRecommendations(Request $request, ?ProjectLocation $projectLocation = null): JsonResponse
    {
        // If project location provided, use it; otherwise get from request or user's active project
        if ($projectLocation) {
            if ($projectLocation->user_id !== Auth::id()) {
                abort(403);
            }
            $latitude = $projectLocation->latitude;
            $longitude = $projectLocation->longitude;
        } else {
            $request->validate([
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                'factory_type_id' => ['nullable', 'string', 'exists:factory_types,uuid'],
            ]);
            $latitude = $request->latitude;
            $longitude = $request->longitude;
        }

        if (!$latitude || !$longitude) {
            return response()->json([
                'success' => false,
                'message' => 'Location coordinates are required.',
            ], 400);
        }

        $factoryService = new FactoryRecommendationService();
        $storeService = new StoreRecommendationService();

        $factoryRecommendations = $factoryService->getContractorRecommendations(
            $latitude,
            $longitude,
            $request->factory_type_id ?? null,
            5
        );

        $storeRecommendations = $storeService->getRecommendations(
            $latitude,
            $longitude,
            5,
            50
        );

        return response()->json([
            'success' => true,
            'factories' => $factoryRecommendations->map(function($item) {
                return [
                    'uuid' => $item['factory']->uuid,
                    'name' => $item['factory']->name,
                    'slug' => $item['factory']->slug,
                    'factory_type' => $item['factory']->factoryType ? $item['factory']->factoryType->name : null,
                    'distance' => $item['distance'],
                    'delivery_cost' => $item['delivery_cost'],
                ];
            }),
            'stores' => $storeRecommendations->map(function($item) {
                return [
                    'uuid' => $item['store']->uuid,
                    'name' => $item['store']->name,
                    'slug' => $item['store']->slug,
                    'distance' => $item['distance'],
                ];
            }),
        ]);
    }
}

