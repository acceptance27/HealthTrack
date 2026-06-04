<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'sex' => ['required', 'in:female,male'],
            'birthdate' => ['required', 'date', 'before:today'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'philhealth_number' => ['nullable', 'string', 'max:50'],
        ];
    }
}
