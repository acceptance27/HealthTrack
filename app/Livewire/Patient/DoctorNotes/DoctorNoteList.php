<?php

namespace App\Livewire\Patient\DoctorNotes;

use App\Models\DoctorNote;
use Livewire\Component;
use Livewire\WithPagination;

class DoctorNoteList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.patient.doctor-notes.doctor-note-list', [
            'records' => DoctorNote::where('patient_id', auth()->id())->latest('noted_at')->paginate(10),
        ]);
    }
}
