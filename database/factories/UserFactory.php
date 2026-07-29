<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /** Hashed once and reused -- hashing per row makes the suite crawl. */
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::Patient,
        ];
    }

    public function midwife(): static
    {
        return $this->state(fn () => ['role' => UserRole::Midwife]);
    }

    public function healthWorker(): static
    {
        return $this->state(fn () => ['role' => UserRole::HealthWorker]);
    }

    public function patient(): static
    {
        return $this->state(fn () => ['role' => UserRole::Patient]);
    }

    /** For testing the "verified" middleware. */
    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
