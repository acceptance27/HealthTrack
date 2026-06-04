<div class="patients-table-container">
    <div class="page-heading">
        <div class="flex items-baseline gap-3">
            <h1>Barangay Patients</h1>
            <span class="mw-muted" style="font-size: 14px; font-weight: 500;">{{ $patients->total() }} Total</span>
        </div>
    </div>

    <div class="card">
        <div class="mw-filter-bar">
                <div class="mw-filter-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mw-muted"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search patients...">
                </div>
                <div class="mw-filter-item">
                    <label>Sex</label>
                    <select wire:model.live="sex" class="mw-filter-select">
                        <option value="">All</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                    </select>
                </div>
                <div class="mw-filter-item">
                    <label>Sort</label>
                    <select wire:model.live="sortBy" class="mw-filter-select">
                        <option value="last_name">Name (A-Z)</option>
                        <option value="age_asc">Age (Youngest)</option>
                        <option value="age_desc">Age (Oldest)</option>
                        <option value="recently_visited">Recently Visited</option>
                    </select>
                </div>
            </div>
        <div class="overflow-x-auto">
            <table class="mw-status-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Birthdate</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patients as $patient)
                        <tr class="cursor-pointer" onclick="window.location='{{ route('midwife.patients.show', $patient) }}'">
                            <td class="font-bold">{{ $patient->fullName() }}</td>
                            <td>{{ ucfirst($patient->sex) }}</td>
                            <td>{{ $patient->birthdate->format('M d, Y') }}</td>
                            <td>{{ $patient->contact_number }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center mw-muted">No patients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
            <div class="mt-6">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</div>
