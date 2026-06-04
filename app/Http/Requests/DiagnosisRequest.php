<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiagnosisRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'diagnosis' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'diagnosed_at' => ['required', 'date'],
        ];
    }
}
