<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'midwife_id' => null,
            'scheduled_at' => fake()->dateTimeBetween('-2 months', '+2 months'),
            'status' => fake()->randomElement(AppointmentStatus::cases()),
            'reason' => fake()->randomElement([
                'Prenatal check-up',
                'Immunization',
                'Blood pressure monitoring',
                'Family planning consultation',
                'Follow-up consultation',
            ]),
            // Not sentence(): Faker's lorem ipsum is Latin and looks broken.
            'notes' => fake()->optional()->randomElement([
                'Patient asked to bring previous laboratory results.',
                'Rescheduled at the request of the patient.',
                'Will be accompanied by a family member.',
                'Reminder to be sent the day before.',
                'Follow-up on the last consultation.',
            ]),
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn () => [
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'status' => AppointmentStatus::Confirmed,
        ]);
    }
}
