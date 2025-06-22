<?php

namespace Database\Factories;

use App\Enums\RolesPermissionEnum;
use App\Enums\TaskLabelEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->text(),
            'due_date' => null,
            'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
            'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
            'user_id' => User::role(RolesPermissionEnum::ADMIN['role'])->first()->id
        ];
    }

    public function withUsers($count = 1): TaskFactory
    {
        return $this->has(User::factory($count), 'users');
    }

    public function withTaskComments($count = 1): TaskFactory
    {
        return $this->afterCreating(function (Task $task) use ($count) {
            for ($i = 0; $i < $count; $i++) {
                TaskComment::factory()
                    ->create([
                        'task_id' => $task->id,
                        'user_id' => $task->users()->inRandomOrder()->first()->id,
                    ]);
            }
        });
    }
}
