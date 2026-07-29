<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\LabValue>
 */
class LabValueFactory extends Factory
{
    public function definition(): array
    {
        $tests = [
            ['Haemoglobin', fake()->randomFloat(1, 9, 16), 'g/dL', '12.0 - 15.5'],
            ['Fasting blood sugar', fake()->numberBetween(70, 180), 'mg/dL', '70 - 100'],
            ['Total cholesterol', fake()->numberBetween(140, 260), 'mg/dL', '< 200'],
            ['Blood pressure', fake()->numberBetween(100, 160).'/'.fake()->numberBetween(60, 100), 'mmHg', '120/80'],
        ];

        [$name, $value, $unit, $range] = fake()->randomElement($tests);

        return [
            'patient_id' => Patient::factory(),
            'created_by' => null,
            'test_name' => $name,
            'value' => (string) $value,
            'unit' => $unit,
            'reference_range' => $range,
            'tested_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
