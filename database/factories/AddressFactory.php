<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class AddressFactory extends Factory
{
    use Translations;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->fakeTranslation('address'),
            'city_id' => City::inRandomOrder()->first()?->id,
            'lat' => fake()->latitude(),
            'lng' => fake()->longitude(),
            'country' => fake()->country(),
            'addressable_id' => User::factory(),
            'addressable_type' => User::class,
            'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1210.6680985414757!2d36.277591805618606!3d33.506483413689416!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2suk!4v1713473796020!5m2!1sen!2suk" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
        ];
    }
}
