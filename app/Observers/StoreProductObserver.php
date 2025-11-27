<?php

namespace App\Observers;

use App\Models\StoreProduct;
use App\Models\StoreProductPriceHistory;

class StoreProductObserver
{
    /**
     * Handle the StoreProduct "updated" event.
     */
    public function updated(StoreProduct $storeProduct): void
    {
        // Track price changes
        if ($storeProduct->isDirty(['price', 'discount_price'])) {
            StoreProductPriceHistory::create([
                'store_product_id' => $storeProduct->uuid,
                'price' => $storeProduct->price,
                'discount_price' => $storeProduct->discount_price,
                'effective_from' => now(),
            ]);

            // Update previous history record's effective_until
            StoreProductPriceHistory::where('store_product_id', $storeProduct->uuid)
                ->whereNull('effective_until')
                ->where('id', '!=', StoreProductPriceHistory::latest()->first()?->id)
                ->update(['effective_until' => now()]);
        }
    }
}

