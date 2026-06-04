<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $barangayId = Auth::user()->barangay_id;
        $appointments = Appointment::where('barangay_id', $barangayId)
            ->with('patient')
            ->orderBy('scheduled_at', 'desc')
            ->paginate(20);

        return view('livewire.midwife.appointments.index', compact('appointments'));
    }

    public function show($id)
    {
        $barangayId = Auth::user()->barangay_id;
        $appointment = Appointment::where('barangay_id', $barangayId)->findOrFail($id);

        return view('midwife.appointments.show', compact('appointment'));
    }
}
