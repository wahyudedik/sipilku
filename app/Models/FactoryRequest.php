<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FactoryRequest extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'user_id',
        'request_group_id',
        'factory_id',
        'factory_type_id',
        'project_location_id',
        'items',
        'message',
        'budget',
        'deadline',
        'status',
        'quoted_price',
        'delivery_cost',
        'additional_fees',
        'total_cost',
        'quote_message',
        'quoted_at',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'order_id',
        'tracking_number',
        'delivery_status',
        'preparing_at',
        'ready_at',
        'in_transit_at',
        'delivered_at',
        'delivery_notes',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'additional_fees' => 'array',
            'budget' => 'decimal:2',
            'quoted_price' => 'decimal:2',
            'delivery_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'deadline' => 'date',
            'quoted_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'preparing_at' => 'datetime',
            'ready_at' => 'datetime',
            'in_transit_at' => 'datetime',
            'delivered_at' => 'datetime',
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

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }

    public function factoryType(): BelongsTo
    {
        return $this->belongsTo(FactoryType::class);
    }

    public function projectLocation(): BelongsTo
    {
        return $this->belongsTo(ProjectLocation::class);
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

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // Order tracking helper methods
    public function isPreparing(): bool
    {
        return $this->delivery_status === 'preparing';
    }

    public function isReady(): bool
    {
        return $this->delivery_status === 'ready';
    }

    public function isInTransit(): bool
    {
        return $this->delivery_status === 'in_transit';
    }

    public function isDelivered(): bool
    {
        return $this->delivery_status === 'delivered';
    }

    // Calculate total cost with breakdown
    public function getCostBreakdownAttribute(): array
    {
        $breakdown = [
            'product_price' => $this->quoted_price ?? 0,
            'delivery_cost' => $this->delivery_cost ?? 0,
            'additional_fees' => 0,
            'total' => $this->total_cost ?? 0,
        ];

        if ($this->additional_fees && is_array($this->additional_fees)) {
            $breakdown['additional_fees'] = array_sum(array_column($this->additional_fees, 'amount'));
        }

        return $breakdown;
    }
}

