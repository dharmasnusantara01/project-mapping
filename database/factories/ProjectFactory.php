<?php

namespace Database\Factories;

use App\Enums\PublicStatus;
use App\Models\Project;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'           => 'Project '.fake()->bs(),
            'customer_name'  => fake()->company(),
            'sector_id'      => Sector::query()->inRandomOrder()->value('id') ?? Sector::factory(),
            'year'           => fake()->numberBetween(2019, (int) date('Y')),
            'public_summary' => fake()->sentence(12),
            'is_public'      => false,
            'public_status'  => fake()->randomElement(PublicStatus::cases()),
            'published_by'   => null,
            'published_at'   => null,
        ];
    }

    public function berjalan(): static
    {
        return $this->state(fn () => ['public_status' => PublicStatus::Berjalan]);
    }

    public function selesai(): static
    {
        return $this->state(fn () => ['public_status' => PublicStatus::Selesai]);
    }

    public function published(?User $publisher = null): static
    {
        return $this->state(fn () => [
            'is_public'    => true,
            'published_by' => $publisher?->id ?? User::query()->where('role', 'manajer_sales')->value('id'),
            'published_at' => now(),
        ]);
    }
}
