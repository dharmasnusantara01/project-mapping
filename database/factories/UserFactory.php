<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nip'               => fake()->unique()->numerify('19#########'),
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'role'              => UserRole::Sales,
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
        ];
    }

    public function manajerSales(): static
    {
        return $this->state(fn () => ['role' => UserRole::ManajerSales]);
    }

    public function superadmin(): static
    {
        return $this->state(fn () => ['role' => UserRole::Superadmin]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
