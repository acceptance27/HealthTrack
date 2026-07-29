<div class="grid gap-4">
    <x-page-header
        title="Patients"
        subtitle="Everyone registered at {{ config('healthtrack.centre.name') }}."
    >
        <x-slot:aside>
            <span class="ht-pill">{{ $patients->total() }} registered</span>
            @can('create', App\Models\Patient::class)
                @if (auth()->user()->isHealthWorker())
                    <a href="{{ route('health-worker.register-patient') }}" class="ht-button">
                        Register Patient
                    </a>
                @endif
            @endcan
        </x-slot:aside>
    </x-page-header>

    <div class="ht-panel">
        <div class="mb-3 grid gap-3 sm:grid-cols-[2fr_1fr]">
            <label class="ht-field">
                <span>Search</span>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Name or contact number"
                    class="ht-input"
                >
            </label>

            <label class="ht-field">
                <span>Sort by</span>
                <select wire:model.live="sortBy" class="ht-input">
                    <option value="last_name">Surname (A-Z)</option>
                    <option value="newest">Recently registered</option>
                    <option value="birthdate">Date of birth</option>
                </select>
            </label>
        </div>

        @if ($patients->isEmpty())
            <div class="ht-empty">
                @if ($search !== '')
                    No patient matches "{{ $search }}".
                @else
                    No patients registered yet.
                @endif
            </div>
        @else
            <div class="ht-table-scroll">
                <table class="ht-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Contact</th>
                            <th>Portal access</th>
                            <th><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($patients as $patient)
                            <tr wire:key="patient-{{ $patient->id }}">
                                <td class="font-bold" style="color: var(--color-brand-strong);">
                                    {{ $patient->fullName() }}
                                </td>
                                <td>{{ $patient->age() }}</td>
                                <td>{{ ucfirst($patient->sex) }}</td>
                                <td>{{ $patient->contact_number ?: '--' }}</td>
                                <td>
                                    @if ($patient->user_id)
                                        <span class="ht-pill">Yes</span>
                                    @else
                                        <span class="ht-muted text-xs">No login</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('patients.show', $patient) }}" class="ht-button ht-button-muted">
                                        Open record
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</div>
