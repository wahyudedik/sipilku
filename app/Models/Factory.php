<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factory extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'user_id',
        'factory_type_id',
        'umkm_id',
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'phone',
        'email',
        'website',
        'category',
        'status',
        'is_verified',
        'is_active',
        'rejection_reason',
        'approved_at',
        'documents',
        'business_license',
        'certifications',
        'rating',
        'total_reviews',
        'total_orders',
        'delivery_price_per_km',
        'max_delivery_distance',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'documents' => 'array',
            'certifications' => 'array',
            'capacity' => 'array',
            'approved_at' => 'datetime',
            'rating' => 'integer',
            'total_reviews' => 'integer',
            'total_orders' => 'integer',
            'delivery_price_per_km' => 'decimal:2',
            'max_delivery_distance' => 'integer',
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

    public function factoryType(): BelongsTo
    {
        return $this->belongsTo(FactoryType::class);
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(FactoryProduct::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(FactoryLocation::class);
    }

    public function primaryLocation(): HasMany
    {
        return $this->hasMany(FactoryLocation::class)->where('is_primary', true);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FactoryReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(FactoryReview::class)->where('is_approved', true);
    }

    public function factoryRequests(): HasMany
    {
        return $this->hasMany(FactoryRequest::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(FactoryView::class);
    }
}
