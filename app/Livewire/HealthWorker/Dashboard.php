<?php

namespace App\Livewire\HealthWorker;

use App\Models\Patient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Health Worker Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.health-worker.dashboard', [
            'patientCount' => Patient::count(),
            'registeredThisMonth' => Patient::where('created_at', '>=', now()->startOfMonth())->count(),
            'withoutPortalLogin' => Patient::whereNull('user_id')->count(),
            'recentPatients' => Patient::latest()->limit(8)->get(),
        ]);
    }
}
