<div class="space-y-6">
    <div class="rounded-lg border border-slate-200 bg-white p-5">
        <h1 class="text-2xl font-semibold">{{ $patient->fullName() }}</h1>
        <p class="mt-1 text-sm text-slate-600">{{ ucfirst($patient->sex) }} · Born {{ $patient->birthdate->format('M d, Y') }} · {{ $patient->address }}</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <x-summary-panel title="Medical History" :records="$medicalHistories" primary="condition" date="recorded_at" />
        <x-summary-panel title="Allergies" :records="$allergies" primary="allergen" date="recorded_at" />
        <x-summary-panel title="Diagnoses" :records="$diagnoses" primary="diagnosis" date="diagnosed_at" />
        <x-summary-panel title="Lab Values" :records="$labValues" primary="test_name" date="tested_at" />
        <x-summary-panel title="Doctor Notes" :records="$doctorNotes" primary="title" date="noted_at" />
    </div>
</div>
