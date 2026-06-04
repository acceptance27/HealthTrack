<?php

namespace App\Livewire\Midwife\Diagnoses;

use App\Models\Diagnosis;
use Livewire\Component;
use Livewire\WithPagination;

class DiagnosisTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.midwife.diagnoses.diagnosis-table', [
            'records' => Diagnosis::forBarangay(auth()->user()->barangay_id)->latest('diagnosed_at')->paginate(10),
        ]);
    }
}
