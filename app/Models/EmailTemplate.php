<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'body',
        'variables',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get template by name.
     */
    public static function getTemplate(string $name): ?self
    {
        return static::where('name', $name)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Render template with variables.
     */
    public function render(array $variables = []): array
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($variables as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Get available variables.
     */
    public function getAvailableVariables(): array
    {
        return $this->variables ?? [];
    }
}
