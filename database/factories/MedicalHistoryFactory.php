<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\MedicalHistory>
 */
class MedicalHistoryFactory extends Factory
{
    public function definition(): array
    {
        // Condition and details are picked as a pair -- see DiagnosisFactory.
        $history = [
            ['Asthma', 'Diagnosed in childhood. Uses an inhaler during episodes, with no hospital admissions in recent years.'],
            ['Chickenpox', 'Had it as a child and recovered fully, with no complications.'],
            ['Dengue fever', 'Admitted for three days and treated with fluids. Made a complete recovery.'],
            ['Appendectomy', 'Appendix removed. The wound healed without complication.'],
            ['Tuberculosis (treated)', 'Completed the full six-month course of treatment and was declared clear.'],
            ['Caesarean section', 'Delivered by caesarean section. Mother and baby were both well afterwards.'],
        ];

        [$condition, $details] = fake()->randomElement($history);

        return [
            'patient_id' => Patient::factory(),
            'created_by' => null,
            'condition' => $condition,
            'details' => $details,
            'recorded_at' => fake()->dateTimeBetween('-5 years', 'now'),
        ];
    }
}
