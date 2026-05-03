<?php

namespace Database\Factories;

use App\Models\CityReference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CityReference>
 */
class CityReferenceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'      => fake()->city(),
            'province'  => fake()->state(),
            'latitude'  => fake()->latitude(-11, 6),
            'longitude' => fake()->longitude(95, 141),
        ];
    }
}
