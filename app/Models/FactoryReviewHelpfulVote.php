<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactoryReviewHelpfulVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'factory_review_id',
        'user_id',
    ];

    // Relationships
    public function factoryReview(): BelongsTo
    {
        return $this->belongsTo(FactoryReview::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
