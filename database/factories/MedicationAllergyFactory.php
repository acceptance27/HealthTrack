<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\MedicationAllergy>
 */
class MedicationAllergyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'created_by' => null,
            'allergen' => fake()->randomElement([
                'Penicillin',
                'Aspirin',
                'Ibuprofen',
                'Sulfa drugs',
                'Peanuts',
                'Shellfish',
            ]),
            'reaction' => fake()->randomElement([
                'Skin rash and itching',
                'Swelling of the lips and face',
                'Shortness of breath',
                'Nausea and vomiting',
            ]),
            'severity' => fake()->randomElement(['mild', 'moderate', 'severe']),
            'recorded_at' => fake()->dateTimeBetween('-3 years', 'now'),
        ];
    }
}
