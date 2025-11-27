<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialRequest extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $fillable = [
        'user_id',
        'store_id',
        'project_location_id',
        'order_id',
        'items',
        'message',
        'budget',
        'deadline',
        'status',
        'quoted_price',
        'quote_message',
        'quoted_at',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'tracking_number',
        'delivery_status',
        'preparing_at',
        'ready_at',
        'in_transit_at',
        'delivered_at',
        'delivery_notes',
        'request_group_id',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'budget' => 'decimal:2',
            'quoted_price' => 'decimal:2',
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

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function projectLocation(): BelongsTo
    {
        return $this->belongsTo(ProjectLocation::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'material_request_id');
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

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isInTransit(): bool
    {
        return $this->delivery_status === 'in_transit';
    }

    public function isDelivered(): bool
    {
        return $this->delivery_status === 'delivered';
    }

    public function getDeliveryStatusLabelAttribute(): string
    {
        return match($this->delivery_status) {
            'pending' => 'Menunggu',
            'preparing' => 'Mempersiapkan',
            'ready' => 'Siap Dikirim',
            'in_transit' => 'Dalam Perjalanan',
            'delivered' => 'Telah Diterima',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }
}

