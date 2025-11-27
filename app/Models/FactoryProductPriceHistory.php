<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactoryProductPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'factory_product_id',
        'price',
        'discount_price',
        'effective_from',
        'effective_until',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'effective_from' => 'datetime',
            'effective_until' => 'datetime',
        ];
    }

    // Relationships
    public function factoryProduct(): BelongsTo
    {
        return $this->belongsTo(FactoryProduct::class);
    }
}
