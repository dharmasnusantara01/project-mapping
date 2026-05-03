<?php

namespace Database\Factories;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Sector>
 */
class SectorFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name'  => ucfirst($name),
            'slug'  => Str::slug($name),
            'color' => fake()->hexColor(),
            'order' => fake()->numberBetween(1, 99),
        ];
    }
}
