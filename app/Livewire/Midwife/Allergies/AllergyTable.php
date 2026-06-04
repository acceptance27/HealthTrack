<?php

namespace App\Livewire\Midwife\Allergies;

use App\Models\MedicationAllergy;
use Livewire\Component;
use Livewire\WithPagination;

class AllergyTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.midwife.allergies.allergy-table', [
            'records' => MedicationAllergy::forBarangay(auth()->user()->barangay_id)->latest('recorded_at')->paginate(10),
        ]);
    }
}
