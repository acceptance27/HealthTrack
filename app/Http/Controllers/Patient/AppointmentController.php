<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Auth::user()->patient->appointments()
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);

        return view('patient.appointments', compact('appointments'));
    }

    public function show($id)
    {
        $appointment = Auth::user()->patient->appointments()->findOrFail($id);
        return view('patient.appointments.show', compact('appointment'));
    }
}
