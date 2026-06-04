<?php

namespace Database\Factories;

use App\Models\Diagnosis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Diagnosis>
 */
class DiagnosisFactory extends Factory
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
            'diagnosis' => fake()->randomElement(['Hypertension', 'Diabetes', 'Common Cold', 'Flu', 'Asthma']),
            'description' => fake()->sentence(),
            'diagnosed_at' => fake()->date(),
        ];
    }
}
