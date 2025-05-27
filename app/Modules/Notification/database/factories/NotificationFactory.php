<?php

namespace App\Modules\Notification\database\factories;

use App\Modules\Notification\App\Config;
use App\Modules\Notification\App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        /** @var class-string<Model> $notifiableClass */
        $notifiableClass = Config::notifiableClass();
        return [
            'id' => fake()->uuid,
            'type' => fake()->word(),
            'notifiable_id' => $notifiableClass::factory()->create()->id,
            'notifiable_type' => $notifiableClass,
            'data' => [
                fake()->word => fake()->word,
            ],
            'read_at' => null,
            'users' => json_encode($notifiableClass::factory(5)->create()->pluck('id')->toArray()),
            'is_handled' => false,
            'model_type' => fake()->word(),
            'model_id' => fake()->randomNumber(),
            'resource' => fake()->word(),
            'resource_id' => fake()->randomNumber(),
        ];
    }
}
