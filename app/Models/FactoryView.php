<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactoryView extends Model
{
    use HasFactory;

    protected $fillable = [
        'factory_id',
        'ip_address',
        'user_agent',
        'user_id',
        'referrer',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    // Relationships
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
