@extends('layouts.app')

@section('title', 'My Health Information')
@section('page-title', 'My Health Information')

@section('content')
@php
    $patient = Auth::user()->patientProfile;
    $sections = [
        'general' => 'General',
        'appointments' => 'Appointments',
        'diagnoses' => 'Diagnoses',
        'doctor-notes' => 'Doctor Notes',
        'lab-values' => 'Lab Values',
        'medical-history' => 'Medical History',
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-[200px_1fr] gap-4 p-4 rounded-2xl bg-gradient-to-br from-amber-50 via-amber-50 to-amber-100">
    <!-- Sidebar Navigation -->
    <aside class="flex flex-col gap-2">
        @foreach($sections as $key => $label)
            <a 
                href="{{ route('patient.my-health-information') }}?section={{ $key }}" 
                class="block px-4 py-3 rounded-lg text-sm font-bold transition-all duration-200 {{ request('section') === $key ? 'bg-white text-teal-700 shadow-lg' : 'text-gray-600 hover:bg-white hover:text-teal-700 hover:shadow-lg' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </aside>

    <!-- Main Content -->
    <main class="flex flex-col gap-4">
        @if(request('section') === 'general' || !request('section'))
            <!-- General Information -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">General Information</h3>
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-3 font-semibold text-gray-700 bg-gray-50">Name</th>
                            <td class="py-2 px-3 text-gray-900">{{ $patient->fullName() }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-3 font-semibold text-gray-700 bg-gray-50">Date of Birth</th>
                            <td class="py-2 px-3 text-gray-900">{{ $patient->birthdate?->format('M d, Y') }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-3 font-semibold text-gray-700 bg-gray-50">Age</th>
                            <td class="py-2 px-3 text-gray-900">{{ $patient->birthdate ? $patient->birthdate->age : 'Unknown' }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-3 font-semibold text-gray-700 bg-gray-50">Sex</th>
                            <td class="py-2 px-3 text-gray-900">{{ ucfirst($patient->sex) }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-3 font-semibold text-gray-700 bg-gray-50">Contact Number</th>
                            <td class="py-2 px-3 text-gray-900">{{ $patient->contact_number }}</td>
                        </tr>
                        <tr>
                            <th class="text-left py-2 px-3 font-semibold text-gray-700 bg-gray-50">Address</th>
                            <td class="py-2 px-3 text-gray-900">{{ $patient->address }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Recent Appointments -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Appointments</h3>
                @if($appointments->count() > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Date</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Status</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments->take(5) as $appointment)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-gray-900">{{ $appointment->scheduled_at->format('M d, Y H:i') }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ ucfirst($appointment->status->value ?? $appointment->status) }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $appointment->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-gray-600 py-8">No appointments found.</div>
                @endif
            </div>

        @elseif(request('section') === 'appointments')
            <!-- All Appointments -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">All Appointments</h3>
                @if($appointments->count() > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Date</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Status</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-gray-900">{{ $appointment->scheduled_at->format('M d, Y H:i') }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ ucfirst($appointment->status->value ?? $appointment->status) }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $appointment->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-gray-600 py-8">No appointments found.</div>
                @endif
            </div>

        @elseif(request('section') === 'diagnoses')
            <!-- Diagnoses -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Diagnoses</h3>
                @if($diagnoses->count() > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Date</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Diagnosis</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Doctor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($diagnoses as $diagnosis)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-gray-900">{{ $diagnosis->diagnosed_at->format('M d, Y') }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $diagnosis->diagnosis }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $diagnosis->created_by ? \App\Models\User::find($diagnosis->created_by)->name : 'Unknown' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-gray-600 py-8">No diagnoses found.</div>
                @endif
            </div>

        @elseif(request('section') === 'doctor-notes')
            <!-- Doctor Notes -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Doctor Notes</h3>
                @if($doctorNotes->count() > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Date</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Note</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Doctor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctorNotes as $note)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-gray-900">{{ $note->noted_at->format('M d, Y') }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $note->note }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $note->created_by ? \App\Models\User::find($note->created_by)->name : 'Unknown' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-gray-600 py-8">No doctor notes found.</div>
                @endif
            </div>

        @elseif(request('section') === 'lab-values')
            <!-- Lab Values -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Lab Values</h3>
                @if($labValues->count() > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Date</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Test</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Value</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Unit</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Normal Range</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($labValues as $lab)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-gray-900">{{ $lab->tested_at->format('M d, Y') }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $lab->test_name }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $lab->value }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $lab->unit }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $lab->reference_range }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-gray-600 py-8">No lab values found.</div>
                @endif
            </div>

        @elseif(request('section') === 'medical-history')
            <!-- Medical History -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Medical History</h3>
                @if($medicalHistories->count() > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Date</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Condition</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicalHistories as $history)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-gray-900">{{ $history->recorded_at->format('M d, Y') }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $history->condition }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $history->details }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-gray-600 py-8">No medical history found.</div>
                @endif
            </div>

            <!-- Allergies -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Allergies</h3>
                @if($allergies->count() > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Allergen</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Reaction</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Severity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allergies as $allergy)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-gray-900">{{ $allergy->allergen }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ $allergy->reaction }}</td>
                                    <td class="py-2 px-3 text-gray-900">{{ ucfirst($allergy->severity) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-gray-600 py-8">No allergies recorded.</div>
                @endif
            </div>
        @endif
    </main>
</div>
@endsection