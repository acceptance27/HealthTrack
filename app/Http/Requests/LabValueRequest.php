<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabValueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'test_name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:50'],
            'reference_range' => ['nullable', 'string', 'max:100'],
            'tested_at' => ['required', 'date'],
        ];
    }
}
