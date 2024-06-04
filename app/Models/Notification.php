<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;


class Notification extends DatabaseNotification
{
    use HasFactory;

    protected $fillable = [
        'type', 'notifiable_id', 'notifiable_type', 'data', 'users', 'read_at', 'is_available',
    ];

    protected $casts = [
        'users'        => 'array',
        'is_available' => 'boolean',
    ];

    /**
     * @param Builder $query
     * @param         $userId
     * @return Builder
     */
    public function scopeByUser(Builder $query, $userId): Builder
    {
        return $query->whereJsonContains('users', $userId);
    }

    /**
     * @param Builder $query
     * @param bool    $value
     * @return Builder
     */
    public function scopeAvailable(Builder $query, bool $value = true): Builder
    {
        return $query->where('is_available', $value);
    }
}
