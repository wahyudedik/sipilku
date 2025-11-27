<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreReviewHelpfulVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_review_id',
        'user_id',
    ];

    // Relationships
    public function storeReview(): BelongsTo
    {
        return $this->belongsTo(StoreReview::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
