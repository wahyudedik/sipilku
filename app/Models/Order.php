<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'quote_request_id',
        'orderable_id',
        'orderable_type',
        'type',
        'amount',
        'discount',
        'total',
        'status',
        'payment_method',
        'notes',
        'completed_at',
        'download_token',
        'download_expires_at',
        'download_count',
        'max_downloads',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'completed_at' => 'datetime',
            'download_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(\App\Models\QuoteRequest::class);
    }

    // Helper methods
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canDownload(): bool
    {
        if ($this->status !== 'completed') {
            return false;
        }

        if ($this->download_expires_at && $this->download_expires_at->isPast()) {
            return false;
        }

        if ($this->download_count >= $this->max_downloads) {
            return false;
        }

        return true;
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('downloads.token', $this->download_token);
    }
}
