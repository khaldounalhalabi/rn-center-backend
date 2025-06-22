<?php

namespace Database\Seeders;

use App\Models\TaskComment;
use Illuminate\Database\Seeder;

class TaskCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskComment::factory(10)->create();
    }
}
