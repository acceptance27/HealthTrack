<?php

namespace App\Livewire\PatientRegistry;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The patient list. Shared by midwives and health workers -- both need to
 * find a patient, they just do different things once they get there.
 */
#[Layout('components.layouts.app')]
#[Title('Patients')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    /** Kept in the query string so a search can be bookmarked or shared. */
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'last_name')]
    public string $sortBy = 'last_name';

    public function mount(): void
    {
        $this->authorize('viewAny', Patient::class);
    }

    /** Reset to page one whenever the search term changes. */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $sortColumn = match ($this->sortBy) {
            'newest' => 'created_at',
            'birthdate' => 'birthdate',
            default => 'last_name',
        };

        $patients = Patient::query()
            ->search($this->search)
            ->orderBy($sortColumn, $this->sortBy === 'newest' ? 'desc' : 'asc')
            ->paginate(15);

        return view('livewire.patient-registry.index', [
            'patients' => $patients,
        ]);
    }
}
