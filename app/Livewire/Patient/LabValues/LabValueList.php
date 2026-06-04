<?php

namespace App\Livewire\Patient\LabValues;

use App\Models\LabValue;
use Livewire\Component;
use Livewire\WithPagination;

class LabValueList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.patient.lab-values.lab-value-list', [
            'records' => LabValue::where('patient_id', auth()->id())->latest('tested_at')->paginate(10),
        ]);
    }
}
