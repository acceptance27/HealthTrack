<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Diagnosis>
 */
class DiagnosisFactory extends Factory
{
    public function definition(): array
    {
        // Diagnosis and description are picked as a pair so the demo data
        // reads sensibly. Faker's sentence() would put Latin lorem ipsum on
        // screen, which looks broken to anyone being shown the system.
        $cases = [
            ['Acute upper respiratory infection', 'Sore throat and dry cough for three days. No fever on examination.'],
            ['Hypertension', 'Blood pressure raised on two separate visits. Advised a low-salt diet and daily monitoring.'],
            ['Iron deficiency anaemia', 'Reports tiredness and breathlessness on exertion. Iron supplements started.'],
            ['Type 2 diabetes mellitus', 'Fasting blood sugar raised. Referred for dietary counselling and regular review.'],
            ['Acute gastroenteritis', 'Loose stools and stomach cramps since yesterday. Oral rehydration given.'],
            ['Urinary tract infection', 'Burning sensation when passing urine. A course of antibiotics was prescribed.'],
        ];

        [$diagnosis, $description] = fake()->randomElement($cases);

        return [
            'patient_id' => Patient::factory(),
            'created_by' => null,
            'diagnosis' => $diagnosis,
            'description' => $description,
            'diagnosed_at' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
