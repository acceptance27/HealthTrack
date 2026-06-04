<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorNoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'note' => ['required', 'string', 'max:5000'],
            'noted_at' => ['required', 'date'],
        ];
    }
}
