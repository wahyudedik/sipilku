<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'inputs',
        'results',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'inputs' => 'array',
            'results' => 'array',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'rab' => 'RAB Calculator',
            'volume_material' => 'Volume Material Calculator',
            'struktur' => 'Struktur Calculator',
            'pondasi' => 'Pondasi Calculator',
            'estimasi_waktu' => 'Estimasi Waktu Proyek',
            'overhead_profit' => 'Overhead & Profit Calculator',
            default => ucfirst($this->type),
        };
    }
}
