<?php

namespace Database\Factories;

use App\Models\MedicalHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalHistory>
 */
class MedicalHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => \App\Models\User::factory()->midwife(),
            'condition' => fake()->randomElement(['Hypertension', 'Diabetes', 'Asthma', 'Allergies', 'Surgery']),
            'details' => fake()->sentence(),
            'recorded_at' => fake()->date(),
        ];
    }
}
