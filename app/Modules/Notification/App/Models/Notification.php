<?php

namespace App\Modules\Notification\App\Models;

use App\Modules\Notification\database\factories\NotificationFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @property string              id
 * @property string              type
 * @property int                 notifiable_id
 * @property class-string        notifiable_type
 * @property array               data
 * @property array               users
 * @property Carbon              read_at
 * @property boolean             is_handled
 * @property int                 model_id
 * @property class-string<Model> model_type
 * @property string              resource
 * @property int                 resource_id
 */
class Notification extends DatabaseNotification
{
    use HasFactory;

    protected $fillable = [
        'type',
        'notifiable_id',
        'notifiable_type',
        'data',
        'users',
        'read_at',
        'is_handled',
        'model_id',
        'model_type',
        'resource',
        'resource_id',
    ];

    protected $casts = [
        'users' => 'array',
        'is_handled' => 'boolean',
        'data' => 'array',
    ];

    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }

    /**
     * @param Builder $query
     * @param         $userId
     * @return Builder
     */
    public function scopeByUser(Builder $query, $userId): Builder
    {
        return $query->whereJsonContains('users', $userId);
    }
}
