<?php

/*
|--------------------------------------------------------------------------
| HealthTrack configuration
|--------------------------------------------------------------------------
|
| This is the single place where the health centre's identity and the shape
| of every clinical record type are defined. If you want to add a field to a
| record, or add a whole new kind of record, you do it here -- the tables,
| forms, and patient portal all read from this file.
|
| See DOCS/03-adding-a-record-type.md for a walkthrough.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Health centre identity
    |--------------------------------------------------------------------------
    |
    | HealthTrack serves exactly one barangay health centre. These values are
    | shown in the page header and on printed output. There is deliberately no
    | "barangays" table -- the system is not multi-tenant.
    |
    */

    'centre' => [
        'name' => env('HEALTHTRACK_CENTRE_NAME', 'Barangay Health Center of Mambog I'),
        'barangay' => env('HEALTHTRACK_BARANGAY', 'Mambog I'),
        'municipality' => env('HEALTHTRACK_MUNICIPALITY', 'Bacoor'),
        'province' => env('HEALTHTRACK_PROVINCE', 'Cavite'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Clinical record types
    |--------------------------------------------------------------------------
    |
    | Every entry below becomes a tab on the patient record screen (for staff)
    | and a read-only section in the patient portal. One shared Livewire
    | component renders all of them, so adding a type here is all it takes.
    |
    | Each field supports:
    |   label    -- what the form and table column say
    |   type     -- text | textarea | select | number
    |   rules    -- standard Laravel validation rules
    |   options  -- required for type "select"
    |   primary  -- true for the one column that identifies the row
    |   column   -- true to show it as its own table column (default false)
    |
    */

    'records' => [

        'diagnoses' => [
            'label' => 'Diagnoses',
            'singular' => 'Diagnosis',
            'model' => App\Models\Diagnosis::class,
            'date_field' => 'diagnosed_at',
            'date_label' => 'Date diagnosed',
            'fields' => [
                'diagnosis' => [
                    'label' => 'Diagnosis',
                    'type' => 'text',
                    'rules' => ['required', 'string', 'max:255'],
                    'primary' => true,
                ],
                'description' => [
                    'label' => 'Description',
                    'type' => 'textarea',
                    'rules' => ['nullable', 'string', 'max:2000'],
                    'column' => true,
                ],
            ],
        ],

        'lab-values' => [
            'label' => 'Lab Values',
            'singular' => 'Lab Value',
            'model' => App\Models\LabValue::class,
            'date_field' => 'tested_at',
            'date_label' => 'Date tested',
            'fields' => [
                'test_name' => [
                    'label' => 'Test name',
                    'type' => 'text',
                    'rules' => ['required', 'string', 'max:255'],
                    'primary' => true,
                ],
                'value' => [
                    'label' => 'Result',
                    'type' => 'text',
                    'rules' => ['required', 'string', 'max:255'],
                    'column' => true,
                ],
                'unit' => [
                    'label' => 'Unit',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:50'],
                    'column' => true,
                ],
                'reference_range' => [
                    'label' => 'Reference range',
                    'type' => 'text',
                    'rules' => ['nullable', 'string', 'max:100'],
                    'column' => true,
                ],
            ],
        ],

        'doctor-notes' => [
            'label' => 'Doctor Notes',
            'singular' => 'Doctor Note',
            'model' => App\Models\DoctorNote::class,
            'date_field' => 'noted_at',
            'date_label' => 'Date noted',
            'fields' => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'rules' => ['required', 'string', 'max:255'],
                    'primary' => true,
                ],
                'note' => [
                    'label' => 'Note',
                    'type' => 'textarea',
                    'rules' => ['required', 'string', 'max:5000'],
                    'column' => true,
                ],
            ],
        ],

        'medical-history' => [
            'label' => 'Medical History',
            'singular' => 'Medical History Entry',
            'model' => App\Models\MedicalHistory::class,
            'date_field' => 'recorded_at',
            'date_label' => 'Date recorded',
            'fields' => [
                'condition' => [
                    'label' => 'Condition',
                    'type' => 'text',
                    'rules' => ['required', 'string', 'max:255'],
                    'primary' => true,
                ],
                'details' => [
                    'label' => 'Details',
                    'type' => 'textarea',
                    'rules' => ['nullable', 'string', 'max:2000'],
                    'column' => true,
                ],
            ],
        ],

        'allergies' => [
            'label' => 'Allergies',
            'singular' => 'Allergy',
            'model' => App\Models\MedicationAllergy::class,
            'date_field' => 'recorded_at',
            'date_label' => 'Date recorded',
            'fields' => [
                'allergen' => [
                    'label' => 'Allergen',
                    'type' => 'text',
                    'rules' => ['required', 'string', 'max:255'],
                    'primary' => true,
                ],
                'reaction' => [
                    'label' => 'Reaction',
                    'type' => 'textarea',
                    'rules' => ['nullable', 'string', 'max:1000'],
                    'column' => true,
                ],
                'severity' => [
                    'label' => 'Severity',
                    'type' => 'select',
                    'rules' => ['nullable', 'in:mild,moderate,severe'],
                    'options' => [
                        'mild' => 'Mild',
                        'moderate' => 'Moderate',
                        'severe' => 'Severe',
                    ],
                    'column' => true,
                ],
            ],
        ],

    ],

];
