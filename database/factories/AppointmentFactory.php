<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'midwife_id' => \App\Models\User::factory()->midwife(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 month'),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'reason' => fake()->sentence(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
