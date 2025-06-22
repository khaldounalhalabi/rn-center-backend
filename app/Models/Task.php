<?php

namespace App\Models;

use App\Enums\PermissionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string      title
 * @property null|string description
 * @property null|string due_date
 * @property string      status
 * @property null|string label
 * @property int         user_id
 * @property User        user
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'label',
        'user_id'
    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'title',
            'description',
            'due_date',
            'status',
            'label',

        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_users')->withTimestamps('assigned_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'title',
            'description',
            'status',
            'label',
        ];
    }

    public function canChangeStatus(): bool
    {
        return isAdmin()
            || can(PermissionEnum::TASKS_MANAGEMENT)
            || $this->users->where('id', user()->id)->count() > 0;
    }

    public function canComment(): bool
    {
        return isAdmin()
            || can(PermissionEnum::TASKS_MANAGEMENT)
            || $this->users->where('id', user()->id)->count() > 0;
    }

    public function canDelete(): bool
    {
        return isAdmin()
            || can(PermissionEnum::TASKS_MANAGEMENT);
    }
}
