<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
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
        'package_prices',
        'preview_image',
        'gallery_images',
        'portfolio',
        'status',
        'completed_orders',
        'rating',
        'review_count',
        'rejection_reason',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'package_prices' => 'array',
            'gallery_images' => 'array',
            'portfolio' => 'array',
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

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }

    // Helper methods
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
