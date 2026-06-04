<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientProfileFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function ($patient): void {
            $email = strtolower(str_replace(' ', '', $patient->last_name) . '.' . str_replace(' ', '', $patient->first_name) . '@healthtrack.test');
            $patient->user->forceFill([
                'barangay_id' => $patient->barangay_id,
                'role' => 'patient',
                'email' => $email,
            ])->save();
        });
    }

    public function definition(): array
    {
        return [
            'barangay_id' => Barangay::factory(),
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->lastName(),
            'last_name' => fake()->lastName(),
            'sex' => fake()->randomElement(['female', 'male']),
            'birthdate' => fake()->dateTimeBetween('-80 years', '-1 year'),
            'contact_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
        ];
    }
}
