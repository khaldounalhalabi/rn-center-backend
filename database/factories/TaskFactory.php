<?php

namespace Database\Factories;

use App\Enums\TaskLabelEnum;
use App\Enums\TaskStatusEnum;
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
        ];
    }

    public function withUsers($count = 1): TaskFactory
    {
        return $this->has(User::factory($count), 'users');
    }
}
