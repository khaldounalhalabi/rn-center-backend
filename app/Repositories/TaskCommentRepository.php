<?php

namespace App\Repositories;

use App\Models\TaskComment;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<TaskComment>
 */
class TaskCommentRepository extends BaseRepository
{
    protected string $modelClass = TaskComment::class;
}
