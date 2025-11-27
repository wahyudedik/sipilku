<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Services\FactoryComparisonService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FactoryComparisonController extends Controller
{
    protected FactoryComparisonService $comparisonService;

    public function __construct(FactoryComparisonService $comparisonService)
    {
        $this->comparisonService = $comparisonService;
    }

    /**
     * Show factory comparison page.
     */
    public function index(Request $request): View
    {
        $factoryIds = $request->get('factory_ids', []);
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $productName = $request->get('product_name');
        $factoryTypeId = $request->get('factory_type_id');
        $quantity = $request->get('quantity', 1);

        $comparisons = collect();

        if (!empty($factoryIds) && is_array($factoryIds)) {
            // Compare specific factories
            $comparisons = $this->comparisonService->compareFactories(
                $factoryIds,
                $latitude ? (float)$latitude : null,
                $longitude ? (float)$longitude : null
            );
        } elseif ($productName && $latitude && $longitude) {
            // Compare by product name and location
            $comparisons = $this->comparisonService->compareTotalCost(
                $productName,
                (float)$latitude,
                (float)$longitude,
                (float)$quantity,
                $factoryTypeId
            );
        }

        return view('factories.comparison.index', compact(
            'comparisons',
            'factoryIds',
            'latitude',
            'longitude',
            'productName',
            'factoryTypeId',
            'quantity'
        ));
    }

    /**
     * Compare factories by price (same product type).
     */
    public function comparePrice(Request $request): View
    {
        $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'quantity' => ['nullable', 'numeric', 'min:0.01'],
            'factory_type_id' => ['nullable', 'string', 'exists:factory_types,uuid'],
        ]);

        $comparisons = $this->comparisonService->compareTotalCost(
            $request->product_name,
            $request->latitude,
            $request->longitude,
            $request->get('quantity', 1),
            $request->factory_type_id
        );

        return view('factories.comparison.price', compact('comparisons'));
    }

    /**
     * Compare factories by quality.
     */
    public function compareQuality(Request $request): View
    {
        $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'factory_type_id' => ['nullable', 'string', 'exists:factory_types,uuid'],
            'factory_ids' => ['nullable', 'array'],
            'factory_ids.*' => ['string', 'exists:factories,uuid'],
        ]);

        $comparisons = $this->comparisonService->compareQuality(
            $request->product_name,
            $request->factory_type_id,
            $request->factory_ids ?? []
        );

        return view('factories.comparison.quality', compact('comparisons'));
    }

    /**
     * Compare factories by multiple criteria (harga, kualitas, jarak).
     */
    public function compareMultiple(Request $request): View
    {
        $request->validate([
            'factory_ids' => ['required', 'array', 'min:2'],
            'factory_ids.*' => ['required', 'string', 'exists:factories,uuid'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $comparisons = $this->comparisonService->compareFactories(
            $request->factory_ids,
            $request->latitude ? (float)$request->latitude : null,
            $request->longitude ? (float)$request->longitude : null
        );

        return view('factories.comparison.multiple', compact('comparisons', 'request'));
    }
}

