<?php

namespace App\Livewire\Midwife\DoctorNotes;

use App\Models\DoctorNote;
use Livewire\Component;
use Livewire\WithPagination;

class DoctorNoteTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.midwife.doctor-notes.doctor-note-table', [
            'records' => DoctorNote::forBarangay(auth()->user()->barangay_id)->latest('noted_at')->paginate(10),
        ]);
    }
}
