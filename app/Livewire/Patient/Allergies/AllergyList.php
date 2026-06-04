<?php

namespace App\Livewire\Patient\Allergies;

use App\Models\MedicationAllergy;
use Livewire\Component;
use Livewire\WithPagination;

class AllergyList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.patient.allergies.allergy-list', [
            'records' => MedicationAllergy::where('patient_id', auth()->id())->latest('recorded_at')->paginate(10),
        ]);
    }
}
