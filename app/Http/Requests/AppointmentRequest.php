<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'scheduled_at' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
            'status' => ['nullable', 'in:pending,confirmed,completed,cancelled'],
        ];
    }
}
