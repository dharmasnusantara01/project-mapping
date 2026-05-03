<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectLocation>
 */
class ProjectLocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id'         => Project::factory(),
            'city'               => fake()->city(),
            'province'           => fake()->state(),
            'latitude'           => fake()->latitude(-4, 4),
            'longitude'          => fake()->longitude(108, 119),
            'is_manual_override' => false,
            'geocoded_at'        => now(),
            'is_primary'         => true,
        ];
    }
}
