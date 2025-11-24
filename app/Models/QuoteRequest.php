<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteRequest extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'service_id',
        'user_id',
        'message',
        'requirements',
        'budget',
        'deadline',
        'status',
        'quoted_price',
        'quote_message',
        'quoted_at',
    ];

    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'budget' => 'decimal:2',
            'quoted_price' => 'decimal:2',
            'deadline' => 'date',
            'quoted_at' => 'datetime',
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
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isQuoted(): bool
    {
        return $this->status === 'quoted';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
}
