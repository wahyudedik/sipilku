<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'phone',
        'email',
        'website',
        'status',
        'is_verified',
        'is_active',
        'rejection_reason',
        'approved_at',
        'documents',
        'business_license',
        'rating',
        'total_reviews',
        'total_orders',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'documents' => 'array',
            'approved_at' => 'datetime',
            'rating' => 'integer',
            'total_reviews' => 'integer',
            'total_orders' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(StoreProduct::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(StoreLocation::class);
    }

    public function primaryLocation(): HasMany
    {
        return $this->hasMany(StoreLocation::class)->where('is_primary', true);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(StoreReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(StoreReview::class)->where('is_approved', true);
    }
}
