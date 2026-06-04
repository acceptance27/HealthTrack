<?php

namespace App\Livewire\Patient\Diagnoses;

use App\Models\Diagnosis;
use Livewire\Component;
use Livewire\WithPagination;

class DiagnosisList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.patient.diagnoses.diagnosis-list', [
            'records' => Diagnosis::where('patient_id', auth()->id())->latest('diagnosed_at')->paginate(10),
        ]);
    }
}
