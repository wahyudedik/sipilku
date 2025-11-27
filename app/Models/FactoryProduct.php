<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FactoryProduct extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'factory_id',
        'name',
        'slug',
        'description',
        'sku',
        'code',
        'product_category',
        'price',
        'discount_price',
        'unit',
        'available_units',
        'specifications',
        'quality_grade',
        'images',
        'is_available',
        'is_featured',
        'stock',
        'min_order',
        'view_count',
        'sold_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'available_units' => 'array',
            'specifications' => 'array',
            'quality_grade' => 'array',
            'images' => 'array',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'stock' => 'integer',
            'min_order' => 'integer',
            'view_count' => 'integer',
            'sold_count' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(FactoryProductPriceHistory::class);
    }

    // Helper methods
    public function getFinalPriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    public function hasDiscount(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }

    public function isInStock(): bool
    {
        return $this->stock === null || $this->stock > 0;
    }
}
