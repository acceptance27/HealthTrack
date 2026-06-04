<?php

namespace App\Livewire\Midwife\MedicalHistory;

use App\Models\MedicalHistory;
use Livewire\Component;
use Livewire\WithPagination;

class MedicalHistoryTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.midwife.medical-history.medical-history-table', [
            'records' => MedicalHistory::forBarangay(auth()->user()->barangay_id)->latest('recorded_at')->paginate(10),
        ]);
    }
}
