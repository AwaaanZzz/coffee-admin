<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'icon',
        'link',
        'is_read'
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function getSafeLinkAttribute(): string
    {
        if (empty($this->link)) {
            return route('notifications.index');
        }

        if (str_starts_with($this->link, 'http://') || str_starts_with($this->link, 'https://')) {
            $parsed = parse_url($this->link);
            $path = $parsed['path'] ?? '/';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            return url(ltrim($path, '/') . $query);
        }

        return url(ltrim($this->link, '/'));
    }
}
