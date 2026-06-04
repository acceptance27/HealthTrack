<?php

namespace App\Livewire\Midwife;

use App\Models\Appointment;
use App\Models\InventoryItem;
use App\Models\PatientProfile;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $barangayId = auth()->user()->barangay_id;

        return view('livewire.midwife.dashboard', [
            'patientsCount' => PatientProfile::forBarangay($barangayId)->count(),
            'appointmentsToday' => Appointment::forBarangay($barangayId)->whereDate('scheduled_at', today())->count(),
            'lowStockCount' => InventoryItem::forBarangay($barangayId)->whereColumn('quantity_on_hand', '<=', 'reorder_level')->count(),
        ]);
    }
}
