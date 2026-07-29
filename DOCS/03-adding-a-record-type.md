# 3. Adding a field, or a whole record type

The five clinical record types — diagnoses, lab values, doctor's notes, medical
history and allergies — are all rendered by one component,
`App\Livewire\ClinicalRecords`, driven by `config/healthtrack.php`.

That means most changes here are config changes.

---

## Adding a field to an existing record type

Say diagnoses should also record whether the condition is chronic.

### Step 1 — Add the column

```bash
php artisan make:migration add_is_chronic_to_diagnoses_table
```

```php
public function up(): void
{
    Schema::table('diagnoses', function (Blueprint $table): void {
        $table->string('is_chronic')->nullable();
    });
}

public function down(): void
{
    Schema::table('diagnoses', function (Blueprint $table): void {
        $table->dropColumn('is_chronic');
    });
}
```

```bash
php artisan migrate
```

### Step 2 — Allow it to be saved

In `app/Models/Diagnosis.php`, add it to `$fillable`:

```php
protected $fillable = [
    'patient_id',
    'created_by',
    'diagnosis',
    'description',
    'is_chronic',      // new
    'diagnosed_at',
];
```

> Forgetting this is the classic Laravel bug: no error, the field is just
> silently dropped on save. If a field saves as blank, check `$fillable` first.

### Step 3 — Declare it in the config

In `config/healthtrack.php`, inside the `diagnoses` entry's `fields` array:

```php
'is_chronic' => [
    'label'   => 'Chronic condition',
    'type'    => 'select',
    'rules'   => ['nullable', 'in:yes,no'],
    'options' => ['yes' => 'Yes', 'no' => 'No'],
    'column'  => true,
],
```

### That's it

The field now appears in the entry form, is validated, saves, and shows as a
table column — on both the midwife's screen and the patient's portal. No Blade
file and no PHP class was edited.

---

## Field options

| Key | Meaning |
|-----|---------|
| `label` | Shown on the form and as the column heading. Required. |
| `type` | `text`, `textarea`, `select`, `number`, `date`. Required. |
| `rules` | Standard Laravel validation rules. Required. |
| `options` | `['value' => 'Label']`. Required when `type` is `select`. |
| `primary` | `true` on the one field that identifies the row. Exactly one per type. |
| `column` | `true` to give it its own table column. Defaults to false. |

A note on `primary`: it is the bold first column, and it is always shown. Every
record type needs exactly one.

---

## Adding a whole new record type

Say the centre wants to track **vital signs**.

### Step 1 — Migration

```bash
php artisan make:migration create_vital_signs_table
```

```php
public function up(): void
{
    Schema::create('vital_signs', function (Blueprint $table): void {
        $table->id();
        $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

        $table->string('measurement');
        $table->string('reading');
        $table->string('unit')->nullable();

        $table->date('measured_at');
        $table->timestamps();

        $table->index(['patient_id', 'measured_at']);
    });
}

public function down(): void
{
    Schema::dropIfExists('vital_signs');
}
```

Keep the skeleton — `patient_id`, `created_by`, one date column — identical to
the others. `ClinicalRecords` relies on it.

### Step 2 — Model

`app/Models/VitalSign.php`

```php
<?php

namespace App\Models;

use App\Models\Concerns\IsClinicalRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalSign extends Model
{
    use HasFactory;
    use IsClinicalRecord;

    protected $fillable = [
        'patient_id',
        'created_by',
        'measurement',
        'reading',
        'unit',
        'measured_at',
    ];

    protected function casts(): array
    {
        return ['measured_at' => 'date'];
    }
}
```

The `IsClinicalRecord` trait supplies the `patient()` and `author()`
relationships. Do not write them by hand.

### Step 3 — Config entry

In `config/healthtrack.php`, add to the `records` array:

```php
'vital-signs' => [
    'label'      => 'Vital Signs',
    'singular'   => 'Vital Sign',
    'model'      => App\Models\VitalSign::class,
    'date_field' => 'measured_at',
    'date_label' => 'Date measured',
    'fields' => [
        'measurement' => [
            'label'   => 'Measurement',
            'type'    => 'select',
            'rules'   => ['required', 'in:blood_pressure,temperature,pulse,weight'],
            'options' => [
                'blood_pressure' => 'Blood pressure',
                'temperature'    => 'Temperature',
                'pulse'          => 'Pulse rate',
                'weight'         => 'Weight',
            ],
            'primary' => true,
        ],
        'reading' => [
            'label'  => 'Reading',
            'type'   => 'text',
            'rules'  => ['required', 'string', 'max:100'],
            'column' => true,
        ],
        'unit' => [
            'label'  => 'Unit',
            'type'   => 'text',
            'rules'  => ['nullable', 'string', 'max:50'],
            'column' => true,
        ],
    ],
],
```

### Step 4 — Relationship on Patient (optional but tidy)

In `app/Models/Patient.php`:

```php
public function vitalSigns(): HasMany
{
    return $this->hasMany(VitalSign::class);
}
```

### Step 5 — Factory (only if you want seeded or tested data)

`database/factories/VitalSignFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class VitalSignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id'  => Patient::factory(),
            'created_by'  => null,
            'measurement' => 'blood_pressure',
            'reading'     => '120/80',
            'unit'        => 'mmHg',
            'measured_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
```

### Done

"Vital Signs" is now a tab on every patient's record, editable by the midwife,
visible read-only in the patient portal, and covered by the existing tests in
`tests/Feature/ClinicalRecordsTest.php` — that suite loops over every type in
the config, so a new type is tested the moment you add it.

---

## What the policy wiring does automatically

`app/Providers/AppServiceProvider.php` contains:

```php
foreach (config('healthtrack.records') as $definition) {
    Gate::policy($definition['model'], ClinicalRecordPolicy::class);
}
```

Because it loops over the config, a new record type picks up
`ClinicalRecordPolicy` with no extra registration. Midwives write, health
workers and patients read.

If one type needs different rules, give it its own policy class named after the
model (`VitalSignPolicy`) — Laravel's auto-discovery finds it, and you should
then exclude that model from the loop above.

---

## Removing a record type

1. Delete its entry from `config/healthtrack.php` — it vanishes from the UI at once.
2. Write a migration that drops the table.
3. Delete the model, its factory, and any `Patient` relationship method.

Do them in that order. Removing the config entry first means the UI never
references a table that is about to disappear.
