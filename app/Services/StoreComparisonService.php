<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreProduct;
use Illuminate\Support\Collection;

class StoreComparisonService
{
    /**
     * Compare prices across multiple stores for a specific product.
     * 
     * @param string $productName Product name to search for
     * @param array $storeIds Optional: specific store UUIDs to compare
     * @return Collection Collection of stores with product prices
     */
    public function comparePrices(string $productName, array $storeIds = []): Collection
    {
        $query = Store::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereHas('products', function($q) use ($productName) {
                $q->where('is_active', true)
                  ->where(function($query) use ($productName) {
                      $query->where('name', 'like', "%{$productName}%")
                            ->orWhere('description', 'like', "%{$productName}%");
                  });
            })
            ->with(['products' => function($q) use ($productName) {
                $q->where('is_active', true)
                  ->where(function($query) use ($productName) {
                      $query->where('name', 'like', "%{$productName}%")
                            ->orWhere('description', 'like', "%{$productName}%");
                  })
                  ->orderByRaw('COALESCE(discount_price, price) ASC');
            }])
            ->with(['primaryLocation' => function($q) {
                $q->where('is_active', true);
            }]);

        if (!empty($storeIds)) {
            $query->whereIn('uuid', $storeIds);
        }

        $stores = $query->get();

        return $stores->map(function($store) {
            $products = $store->products;
            $cheapestProduct = $products->first();
            
            return [
                'store' => $store,
                'products' => $products,
                'cheapest_price' => $cheapestProduct ? ($cheapestProduct->final_price ?? $cheapestProduct->price) : null,
                'product_count' => $products->count(),
                'location' => $store->primaryLocation->first(),
            ];
        })
        ->filter(function($item) {
            return $item['cheapest_price'] !== null;
        })
        ->sortBy('cheapest_price')
        ->values();
    }

    /**
     * Compare stores by multiple criteria.
     * 
     * @param array $storeIds Store UUIDs to compare
     * @return Collection
     */
    public function compareStores(array $storeIds): Collection
    {
        $stores = Store::whereIn('uuid', $storeIds)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['primaryLocation' => function($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['approvedReviews'])
            ->get();

        return $stores->map(function($store) {
            return [
                'store' => $store,
                'rating' => $store->rating ?? 0,
                'total_reviews' => $store->approved_reviews_count ?? 0,
                'product_count' => $store->products_count ?? 0,
                'total_orders' => $store->total_orders ?? 0,
                'location' => $store->primaryLocation->first(),
            ];
        })
        ->values();
    }
}

