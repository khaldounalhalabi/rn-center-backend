<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class EnquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bool = fake()->boolean;
        return [
            'name'    => $bool ? "khaldoun" : "mustafa",
            'email'   => $bool ? "khaldoun1222@hotmail.com" : "asasimr55@gmail.com",
            'message' => fake()->text(),
        ];
    }
}
