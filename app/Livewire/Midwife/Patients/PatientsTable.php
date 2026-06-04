<?php

namespace App\Livewire\Midwife\Patients;

use App\Models\PatientProfile;
use Livewire\Component;
use Livewire\WithPagination;

class PatientsTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sex = '';
    public string $sortBy = 'last_name';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'sex' => ['except' => ''],
        'sortBy' => ['except' => 'last_name'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSex(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $barangayId = auth()->user()->barangay_id;

        $query = PatientProfile::query()
            ->forBarangay($barangayId)
            ->when(trim($this->search), function ($query) {
                $search = trim($this->search);
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($this->sex, fn ($query) => $query->where('sex', $this->sex));

        if ($this->sortBy === 'age_desc') {
            $query->orderBy('birthdate', 'asc'); // Older first
        } elseif ($this->sortBy === 'age_asc') {
            $query->orderBy('birthdate', 'desc'); // Younger first
        } elseif ($this->sortBy === 'recently_visited') {
            $query->withLastAppointmentDate()->orderByDesc('last_appointment_at');
        } else {
            $query->orderBy('last_name');
        }

        return view('livewire.midwife.patients.patients-table', [
            'patients' => $query->paginate($this->perPage),
        ]);
    }
}
