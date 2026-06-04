<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BarangayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Barangay '.$this->faker->unique()->numberBetween(1, 999),
            'municipality' => $this->faker->randomElement(['San Pablo', 'Santa Rosa', 'Calamba', 'Binan']),
            'province' => $this->faker->state(),
            'region' => 'Region IV-A',
        ];
    }
}
