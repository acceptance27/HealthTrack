<?php

namespace App\Livewire\Midwife\LabValues;

use App\Models\LabValue;
use Livewire\Component;
use Livewire\WithPagination;

class LabValueTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.midwife.lab-values.lab-value-table', [
            'records' => LabValue::forBarangay(auth()->user()->barangay_id)->latest('tested_at')->paginate(10),
        ]);
    }
}
