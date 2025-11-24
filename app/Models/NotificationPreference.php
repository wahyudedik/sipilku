<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'email_enabled',
        'database_enabled',
    ];

    protected function casts(): array
    {
        return [
            'email_enabled' => 'boolean',
            'database_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get default preferences for a notification type.
     */
    public static function getDefault(string $type): array
    {
        return [
            'email_enabled' => true,
            'database_enabled' => true,
        ];
    }

    /**
     * Get user preference for a notification type.
     */
    public static function getUserPreference(int $userId, string $type): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId, 'type' => $type],
            static::getDefault($type)
        );
    }
}
