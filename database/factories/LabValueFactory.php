<?php

namespace Database\Factories;

use App\Models\LabValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabValue>
 */
class LabValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tests = [
            ['name' => 'Blood Pressure', 'unit' => 'mmHg', 'range' => '120/80'],
            ['name' => 'Blood Sugar', 'unit' => 'mg/dL', 'range' => '70-100'],
            ['name' => 'Cholesterol', 'unit' => 'mg/dL', 'range' => '<200'],
            ['name' => 'Hemoglobin', 'unit' => 'g/dL', 'range' => '12-16'],
        ];
        $test = fake()->randomElement($tests);
        
        return [
            'created_by' => \App\Models\User::factory()->midwife(),
            'test_name' => $test['name'],
            'value' => fake()->numberBetween(50, 200),
            'unit' => $test['unit'],
            'reference_range' => $test['range'],
            'tested_at' => fake()->date(),
        ];
    }
}
