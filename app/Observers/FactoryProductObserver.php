<?php

namespace App\Observers;

use App\Models\FactoryProduct;
use App\Models\FactoryProductPriceHistory;

class FactoryProductObserver
{
    /**
     * Handle the FactoryProduct "updated" event.
     */
    public function updated(FactoryProduct $factoryProduct): void
    {
        // Track price changes
        if ($factoryProduct->isDirty(['price', 'discount_price'])) {
            FactoryProductPriceHistory::create([
                'factory_product_id' => $factoryProduct->uuid,
                'price' => $factoryProduct->price,
                'discount_price' => $factoryProduct->discount_price,
                'effective_from' => now(),
            ]);

            // Update previous history record's effective_until
            FactoryProductPriceHistory::where('factory_product_id', $factoryProduct->uuid)
                ->whereNull('effective_until')
                ->where('id', '!=', FactoryProductPriceHistory::latest()->first()?->id)
                ->update(['effective_until' => now()]);
        }
    }
}

