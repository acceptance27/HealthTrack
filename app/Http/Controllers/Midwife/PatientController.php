<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\PatientProfile;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index()
    {
        $barangayId = Auth::user()->barangay_id;
        $patients = PatientProfile::where('barangay_id', $barangayId)
            ->paginate(20);

        return view('livewire.midwife.patients.index', compact('patients'));
    }

    public function show(Request $request, $id)
    {
        $barangayId = Auth::user()->barangay_id;
        $patient = PatientProfile::where('barangay_id', $barangayId)->findOrFail($id);
        $section = $request->query('section', 'general');
        $allowedSections = ['general', 'diagnoses', 'doctor-notes', 'lab-values', 'medical-history'];
        $section = in_array($section, $allowedSections, true) ? $section : 'general';
        $calendarMonth = Carbon::parse($request->query('month', now()->format('Y-m')) . '-01');

        return view('livewire.midwife.patients.show', [
            'patient' => $patient,
            'section' => $section,
            'calendarMonth' => $calendarMonth,
            'allergies' => $patient->allergies()->latest('recorded_at')->get(),
            'appointments' => $patient->appointments()->latest('scheduled_at')->get(),
            'calendarAppointments' => $patient->appointments()
                ->whereBetween('scheduled_at', [
                    $calendarMonth->copy()->startOfMonth(),
                    $calendarMonth->copy()->endOfMonth(),
                ])
                ->get()
                ->groupBy(fn (Appointment $appointment) => $appointment->scheduled_at->format('Y-m-d')),
            'diagnoses' => $patient->diagnoses()->latest('diagnosed_at')->get(),
            'doctorNotes' => $patient->doctorNotes()->latest('noted_at')->get(),
            'labValues' => $patient->labValues()->latest('tested_at')->get(),
            'medicalHistories' => $patient->medicalHistories()->latest('recorded_at')->get(),
        ]);
    }

    public function storeAppointment(Request $request, $id): RedirectResponse
    {
        $barangayId = Auth::user()->barangay_id;
        $patient = PatientProfile::where('barangay_id', $barangayId)->findOrFail($id);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
        ]);

        Appointment::create([
            'barangay_id' => $barangayId,
            'patient_id' => $patient->user_id,
            'midwife_id' => Auth::id(),
            'scheduled_at' => $data['scheduled_at'],
            'status' => $data['status'],
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('midwife.patients.show', ['id' => $patient->id, 'section' => 'general'])
            ->with('status', 'Appointment added.');
    }

    public function destroyAppointment($id, Appointment $appointment): RedirectResponse
    {
        $barangayId = Auth::user()->barangay_id;
        $patient = PatientProfile::where('barangay_id', $barangayId)->findOrFail($id);

        abort_unless(
            $appointment->barangay_id === $barangayId && $appointment->patient_id === $patient->user_id,
            404
        );

        $appointment->delete();

        return redirect()
            ->route('midwife.patients.show', ['id' => $patient->id, 'section' => 'general'])
            ->with('status', 'Appointment removed.');
    }
}
