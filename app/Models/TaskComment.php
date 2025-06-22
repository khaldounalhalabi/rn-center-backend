<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    user_id
 * @property int    task_id
 * @property string comment
 * @property User   user
 * @property Task   task
 */
class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'comment',
    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'comment',
            'user.first_name',
            'task.title',

        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [

            //filesKeys
        ];
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'comment',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [

        ];
    }

    public function canUpdate(): bool
    {
        return $this->user_id == user()->id;
    }

    public function canDelete(): bool
    {
        return $this->user_id == user()->id;
    }
}
