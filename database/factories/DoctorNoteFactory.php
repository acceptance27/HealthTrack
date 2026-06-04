<?php

namespace Database\Factories;

use App\Models\DoctorNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorNote>
 */
class DoctorNoteFactory extends Factory
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
            'title' => fake()->sentence(3),
            'note' => fake()->paragraph(),
            'noted_at' => fake()->date(),
        ];
    }
}
