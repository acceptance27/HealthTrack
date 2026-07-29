<?php

namespace App\Livewire\Midwife;

use App\Models\Appointment;
use App\Models\Patient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Midwife Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.midwife.dashboard', [
            'patientCount' => Patient::count(),
            'appointmentsToday' => Appointment::today()->count(),
            'upcomingCount' => Appointment::upcoming()->count(),
            'todaysAppointments' => Appointment::today()
                ->with('patient')
                ->orderBy('scheduled_at')
                ->get(),
            'recentPatients' => Patient::latest()->limit(5)->get(),
        ]);
    }
}
