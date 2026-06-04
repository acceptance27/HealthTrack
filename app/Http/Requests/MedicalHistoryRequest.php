<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalHistoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'condition' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:2000'],
            'recorded_at' => ['required', 'date'],
        ];
    }
}
