<?php

namespace App\Livewire\Patient\MedicalHistory;

use App\Models\MedicalHistory;
use Livewire\Component;
use Livewire\WithPagination;

class MedicalHistoryList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.patient.medical-history.medical-history-list', [
            'records' => MedicalHistory::where('patient_id', auth()->id())->latest('recorded_at')->paginate(10),
        ]);
    }
}
