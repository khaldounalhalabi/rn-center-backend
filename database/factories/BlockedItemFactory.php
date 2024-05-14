<?php

namespace Database\Factories;

use App\Enums\BlockTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends Factory
 */
class BlockedItemFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['type' => "string", 'value' => "string"])]
    public function definition(): array
    {
        $type = fake()->randomElement(BlockTypeEnum::getAllValues());
        $value = $type == BlockTypeEnum::EMAIL->value
            ? fake()->email
            : (
            $type == BlockTypeEnum::PHONE->value
                ? fake()->phoneNumber()
                : fake()->name
            );
        return [
            'type' => $type,
            'value' => $value,
        ];
    }
}
