<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'short_description',
        'price',
        'discount_price',
        'preview_image',
        'gallery_images',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'status',
        'download_count',
        'sales_count',
        'rating',
        'review_count',
        'rejection_reason',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'gallery_images' => 'array',
            'rating' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Helper methods
    public function getFinalPriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
