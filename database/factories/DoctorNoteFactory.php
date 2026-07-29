<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\DoctorNote>
 */
class DoctorNoteFactory extends Factory
{
    public function definition(): array
    {
        // Title and body are picked together so the note reads as one piece.
        $notes = [
            ['Consultation summary', 'Patient seen for a routine check-up. Vital signs were within normal limits and no new complaints were raised. Advised to return in one month.'],
            ['Follow-up observation', 'Symptoms have improved since the last visit and the medication is being taken as prescribed. The current plan will continue for another two weeks.'],
            ['Referral note', 'Referred to the district hospital for further assessment. Copies of the recent laboratory results were given to the patient to bring along.'],
            ['Home care instructions', 'Advised plenty of rest and fluids. Explained the warning signs that would mean returning to the health centre straight away.'],
        ];

        [$title, $note] = fake()->randomElement($notes);

        return [
            'patient_id' => Patient::factory(),
            'created_by' => null,
            'title' => $title,
            'note' => $note,
            'noted_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
