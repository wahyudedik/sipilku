<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MaterialRequest;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreProduct extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'store_id',
        'store_category_id',
        'name',
        'slug',
        'description',
        'sku',
        'brand',
        'price',
        'discount_price',
        'unit',
        'stock',
        'min_order',
        'images',
        'specifications',
        'is_active',
        'is_featured',
        'view_count',
        'sold_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'stock' => 'integer',
            'min_order' => 'integer',
            'images' => 'array',
            'specifications' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'view_count' => 'integer',
            'sold_count' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(StoreCategory::class, 'store_category_id');
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(StoreProductPriceHistory::class);
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
        return $this->stock > 0 || $this->stock === null;
    }

    public function getAvailabilityStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'unavailable';
        }

        if ($this->stock === null) {
            return 'available';
        }

        if ($this->stock <= 0) {
            return 'out_of_stock';
        }

        if ($this->stock <= 10) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    public function getAvailabilityLabelAttribute(): string
    {
        return match($this->availability_status) {
            'unavailable' => 'Tidak Tersedia',
            'out_of_stock' => 'Habis',
            'low_stock' => 'Stok Menipis',
            'in_stock' => 'Tersedia',
            'available' => 'Tersedia',
            default => 'Tidak Diketahui',
        };
    }
}
