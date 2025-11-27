<?php

namespace App\Http\Controllers;

use App\Services\FactoryComparisonService;
use App\Services\StoreComparisonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComparisonController extends Controller
{
    /**
     * Compare prices across stores.
     */
    public function compareStorePrices(Request $request): JsonResponse|View
    {
        $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'store_ids' => ['nullable', 'array'],
            'store_ids.*' => ['string', 'exists:stores,uuid'],
        ]);

        $service = new StoreComparisonService();
        $comparisons = $service->comparePrices(
            $request->product_name,
            $request->store_ids ?? []
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'product_name' => $request->product_name,
                'comparisons' => $comparisons->map(function($item) {
                    return [
                        'store' => [
                            'uuid' => $item['store']->uuid,
                            'name' => $item['store']->name,
                            'slug' => $item['store']->slug,
                            'logo' => $item['store']->logo,
                            'rating' => $item['store']->rating,
                        ],
                        'cheapest_price' => $item['cheapest_price'],
                        'product_count' => $item['product_count'],
                        'location' => $item['location'] ? [
                            'address' => $item['location']->full_address,
                            'city' => $item['location']->city,
                        ] : null,
                    ];
                }),
                'count' => $comparisons->count(),
            ]);
        }

        return view('comparisons.store-prices', [
            'productName' => $request->product_name,
            'comparisons' => $comparisons,
        ]);
    }

    /**
     * Compare stores by multiple criteria.
     */
    public function compareStores(Request $request): JsonResponse|View
    {
        $request->validate([
            'store_ids' => ['required', 'array', 'min:2'],
            'store_ids.*' => ['string', 'exists:stores,uuid'],
        ]);

        $service = new StoreComparisonService();
        $comparisons = $service->compareStores($request->store_ids);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comparisons' => $comparisons->map(function($item) {
                    return [
                        'store' => [
                            'uuid' => $item['store']->uuid,
                            'name' => $item['store']->name,
                            'slug' => $item['store']->slug,
                            'logo' => $item['store']->logo,
                        ],
                        'rating' => $item['rating'],
                        'total_reviews' => $item['total_reviews'],
                        'product_count' => $item['product_count'],
                        'total_orders' => $item['total_orders'],
                        'location' => $item['location'] ? [
                            'address' => $item['location']->full_address,
                        ] : null,
                    ];
                }),
            ]);
        }

        return view('comparisons.stores', compact('comparisons'));
    }

    /**
     * Compare total cost (product price + delivery) across factories.
     */
    public function compareFactoryTotalCost(Request $request): JsonResponse|View
    {
        $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'factory_type_id' => ['nullable', 'string', 'exists:factory_types,uuid'],
            'factory_ids' => ['nullable', 'array'],
            'factory_ids.*' => ['string', 'exists:factories,uuid'],
        ]);

        $service = new FactoryComparisonService();
        $comparisons = $service->compareTotalCost(
            $request->product_name,
            $request->latitude,
            $request->longitude,
            $request->quantity,
            $request->factory_type_id,
            $request->factory_ids ?? []
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'product_name' => $request->product_name,
                'quantity' => $request->quantity,
                'comparisons' => $comparisons->map(function($item) {
                    return [
                        'factory' => [
                            'uuid' => $item['factory']->uuid,
                            'name' => $item['factory']->name,
                            'slug' => $item['factory']->slug,
                            'logo' => $item['factory']->logo,
                            'factory_type' => $item['factory']->factoryType ? $item['factory']->factoryType->name : null,
                            'rating' => $item['factory']->rating,
                        ],
                        'product' => [
                            'name' => $item['product']->name,
                            'price' => $item['product_price'],
                        ],
                        'product_total' => $item['product_total'],
                        'delivery_cost' => $item['delivery_cost'],
                        'total_cost' => $item['total_cost'],
                        'distance' => $item['distance'],
                        'location' => $item['location'] ? [
                            'address' => $item['location']->full_address,
                        ] : null,
                    ];
                }),
                'count' => $comparisons->count(),
            ]);
        }

        return view('comparisons.factory-total-cost', [
            'productName' => $request->product_name,
            'quantity' => $request->quantity,
            'comparisons' => $comparisons,
        ]);
    }

    /**
     * Compare quality across factories (same product type).
     */
    public function compareFactoryQuality(Request $request): JsonResponse|View
    {
        $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'factory_type_id' => ['nullable', 'string', 'exists:factory_types,uuid'],
            'factory_ids' => ['nullable', 'array'],
            'factory_ids.*' => ['string', 'exists:factories,uuid'],
        ]);

        $service = new FactoryComparisonService();
        $comparisons = $service->compareQuality(
            $request->product_name,
            $request->factory_type_id,
            $request->factory_ids ?? []
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'product_name' => $request->product_name,
                'comparisons' => $comparisons->map(function($item) {
                    return [
                        'factory' => [
                            'uuid' => $item['factory']->uuid,
                            'name' => $item['factory']->name,
                            'slug' => $item['factory']->slug,
                            'logo' => $item['factory']->logo,
                            'factory_type' => $item['factory']->factoryType ? $item['factory']->factoryType->name : null,
                        ],
                        'rating' => $item['rating'],
                        'total_reviews' => $item['total_reviews'],
                        'certifications' => $item['certifications'],
                        'certification_count' => $item['certification_count'],
                        'quality_score' => $item['quality_score'],
                    ];
                }),
                'count' => $comparisons->count(),
            ]);
        }

        return view('comparisons.factory-quality', [
            'productName' => $request->product_name,
            'comparisons' => $comparisons,
        ]);
    }

    /**
     * Compare factories by multiple criteria (delivery cost, quality, rating).
     */
    public function compareFactories(Request $request): JsonResponse|View
    {
        $request->validate([
            'factory_ids' => ['required', 'array', 'min:2'],
            'factory_ids.*' => ['string', 'exists:factories,uuid'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $service = new FactoryComparisonService();
        $comparisons = $service->compareFactories(
            $request->factory_ids,
            $request->latitude,
            $request->longitude
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comparisons' => $comparisons->map(function($item) {
                    return [
                        'factory' => [
                            'uuid' => $item['factory']->uuid,
                            'name' => $item['factory']->name,
                            'slug' => $item['factory']->slug,
                            'logo' => $item['factory']->logo,
                            'factory_type' => $item['factory']->factoryType ? $item['factory']->factoryType->name : null,
                        ],
                        'rating' => $item['rating'],
                        'total_reviews' => $item['total_reviews'],
                        'product_count' => $item['product_count'],
                        'certification_count' => $item['certification_count'],
                        'quality_score' => $item['quality_score'],
                        'delivery_price_per_km' => $item['delivery_price_per_km'],
                        'max_delivery_distance' => $item['max_delivery_distance'],
                        'delivery_cost' => $item['delivery_cost'],
                        'distance' => $item['distance'],
                        'location' => $item['location'] ? [
                            'address' => $item['location']->full_address,
                        ] : null,
                    ];
                }),
            ]);
        }

        return view('comparisons.factories', compact('comparisons'));
    }
}

