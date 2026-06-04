<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicationAllergyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'allergen' => ['required', 'string', 'max:255'],
            'reaction' => ['nullable', 'string', 'max:1000'],
            'severity' => ['nullable', 'in:mild,moderate,severe'],
            'recorded_at' => ['required', 'date'],
        ];
    }
}
