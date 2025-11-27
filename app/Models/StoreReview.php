<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreReview extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'store_id',
        'user_id',
        'rating',
        'comment',
        'ratings_breakdown',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'ratings_breakdown' => 'array',
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
            'helpful_count' => 'integer',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(StoreReviewHelpfulVote::class);
    }

    /**
     * Check if a user has marked this review as helpful.
     */
    public function isMarkedHelpfulBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->helpfulVotes()->where('user_id', $userId)->exists();
    }
}
