# 5. Conventions

House rules, with the reasoning. Most of these exist because the previous
version of the project did the opposite and it caused a specific problem.

---

## Naming

| Thing | Convention | Example |
|---|---|---|
| Livewire class | `StudlyCase`, under a role folder | `App\Livewire\Midwife\Appointments` |
| Its Blade file | kebab-case, mirroring the namespace | `livewire/midwife/appointments.blade.php` |
| Model | Singular | `Patient`, `Diagnosis`, `LabValue` |
| Table | Plural snake_case | `patients`, `diagnoses`, `lab_values` |
| Route name | dot-separated | `patients.show`, `midwife.appointments` |
| CSS class | `.ht-` prefix | `.ht-panel`, `.ht-button` |
| Config record key | kebab-case | `lab-values`, `doctor-notes` |

Note `lab-values` (config key, matches the URL) against `lab_values` (table)
against `LabValue` (model). Laravel converts between these itself; follow each
convention in its own context rather than trying to make them identical.

---

## One page, one component

Every route points at a Livewire class. There is no `app/Http/Controllers`.

The previous version had both — a `PatientController` *and* a
`PatientsTable` Livewire component, plus a `PatientRecord` component that no
route ever reached. Nobody could tell which was live. Roughly a third of the
classes in the old `app/` directory were unreachable.

If you are tempted to add a controller, put the logic on the Livewire class
instead.

---

## Authorize in `mount()` *and* in every write method

```php
public function mount(Patient $patient): void
{
    $this->authorize('view', $patient);
    $this->patient = $patient;
}

public function save(): void
{
    $this->authorize('create', Diagnosis::class);   // again -- not redundant
    // ...
}
```

Livewire methods are reachable by a crafted HTTP request without going through
the page that normally exposes them. A check that only runs in `mount()`
protects the page, not the action.

`tests/Feature/PatientPortalTest.php` has a test that calls `save()` directly
as a patient and asserts a 403. Keep that pattern for new write methods.

---

## Never trust an ID from the browser

```php
// Wrong -- lets a midwife delete any record by guessing an ID
$record = Diagnosis::findOrFail($id);

// Right -- scope to the patient whose page this is, first
$record = Diagnosis::where('patient_id', $this->patient->id)->findOrFail($id);
```

The scoped version returns 404 for someone else's record instead of deleting
it. Both `ClinicalRecords::delete()` and `Record::deleteAppointment()` do this,
and both have tests covering it.

---

## Prefer config over copy-paste

Five record types share one component because they have identical structure.
Before writing a fifth near-identical class, ask whether it belongs in
`config/healthtrack.php`.

The counter-rule: **don't abstract two things into one just because they look
similar today.** Patient demographics are a plain form, not config-driven, and
that is correct — they are a fixed set of fields, not a growing list.

---

## Keep SQL portable

Tests run on SQLite; development and production run on PostgreSQL. Avoid
PostgreSQL-only SQL in application code or the suite stops reflecting reality.

The one that bites is case-insensitive search. `LIKE` is case-sensitive on
PostgreSQL but not on SQLite, and `ILIKE` does not exist on SQLite. So:

```php
// Works identically on both
$q->orWhereRaw("lower({$column}) like ?", ['%'.mb_strtolower($term).'%']);
```

See `Patient::scopeSearch()`. There is a test (`PatientListTest`, "searches
without regard to capitalisation") that fails if someone reverts this.

---

## Blade

**One root element per Livewire view.** Non-negotiable — Livewire cannot track
the DOM otherwise.

**`wire:key` on anything in a loop** that can be added or removed:

```blade
@foreach ($records as $record)
    <tr wire:key="{{ $type }}-{{ $record->id }}">
```

Without it, Livewire reuses DOM nodes incorrectly after a delete and you get
rows showing the wrong data.

**No `<style>` blocks.** Add a `.ht-*` class to `app.css` instead.

**Wrap tables in `.ht-table-scroll`** so wide tables scroll rather than the
whole page.

---

## Comments

Explain *why*, not *what*.

```php
// Bad -- restates the code
// Loop through the records
foreach ($records as $record) {

// Good -- explains a decision
// Nulled rather than cascaded so deleting a user never destroys
// clinical history.
$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
```

Where a rule comes from the study, say so. Where something is deliberately
*absent* — public registration, the barangay column — leave a comment saying
why, or someone will helpfully add it back.

---

## Migrations

Never edit a migration that has already run on someone else's machine. Add a
new one.

`migrate:fresh` is fine locally, catastrophic in production. Nothing in this
project should ever run it automatically.

Keep the clinical-record skeleton (`patient_id`, `created_by`, one date column)
identical across types — `ClinicalRecords` depends on it.

---

## Tests

Write one when you add a write path or a permission rule. The suite exists to
catch the two mistakes this project has actually made:

1. A role reaching something it should not.
2. A form that silently fails to save.

Naming: `it('does the thing')`, lower case, describing behaviour.

```php
it('stops a health worker from writing clinical records', function () {
```

Use factories, never hand-built arrays:

```php
$midwife = User::factory()->midwife()->create();
$patient = Patient::factory()->withLogin()->create();
```

---

## Things deliberately absent

Do not add these back without a deliberate decision — each was removed for a
reason recorded here.

| Absent | Why |
|---|---|
| Public registration | Let anyone self-assign the midwife role and read every record |
| A `/login` route in `web.php` | Bypassed Fortify and silently disabled 2FA |
| `barangay_id` columns | The system serves one centre; the study says so |
| Inventory module | Removed from the study's scope |
| `app/Http/Controllers` | Duplicated the Livewire layer; nobody knew which was live |
| `tailwind.config.js` | Tailwind v4 configures in CSS |
| `<style>` in Blade | Three copies had already drifted apart |
