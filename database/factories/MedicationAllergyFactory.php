<?php

namespace Database\Factories;

use App\Models\MedicationAllergy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicationAllergy>
 */
class MedicationAllergyFactory extends Factory
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
            'allergen' => fake()->randomElement(['Penicillin', 'Aspirin', 'Ibuprofen', 'Shellfish', 'Peanuts']),
            'reaction' => fake()->randomElement(['Rash', 'Hives', 'Swelling', 'Difficulty breathing']),
            'severity' => fake()->randomElement(['mild', 'moderate', 'severe']),
            'recorded_at' => fake()->date(),
        ];
    }
}
