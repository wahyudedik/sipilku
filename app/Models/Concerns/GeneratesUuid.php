<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait GeneratesUuid
{
    /**
     * Boot the trait.
     */
    protected static function bootGeneratesUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // Also handle saving event as fallback
        static::saving(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}

