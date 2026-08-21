<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            // No portal login by default -- most walk-in patients will not
            // have one. Use ->withLogin() when you need it.
            'user_id' => null,
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->lastName(),
            'last_name' => fake()->lastName(),
            'sex' => fake()->randomElement(['female', 'male']),
            'birthdate' => fake()->dateTimeBetween('-80 years', '-1 year'),
            'civil_status' => fake()->randomElement(['single', 'married', 'widowed', 'separated']),
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'occupation' => fake()->jobTitle(),
            'barangay_id_number' => fake()->numerify('BRGY-########'),
            'contact_number' => fake()->numerify('09## ### ####'),
            'address' => fake()->streetAddress().', '.config('healthtrack.centre.barangay'),
            'philhealth_number' => fake()->optional()->numerify('##-#########-#'),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_number' => fake()->numerify('09## ### ####'),
        ];
    }

    /** Give this patient a portal account. */
    public function withLogin(): static
    {
        return $this->state(fn () => [
            'user_id' => User::factory()->patient(),
        ]);
    }
}
