# 1. How HealthTrack works

Read this first. It explains the shape of the project so the other documents
make sense.

---

## The one rule that explains the layout

**One page = one Livewire class + one Blade file.**

There is no `app/Http/Controllers` directory. A route points straight at a
Livewire component:

```php
Route::get('/patients', PatientsIndex::class)->name('patients.index');
```

That component holds the page's data *and* its behaviour. When a button is
clicked, Livewire calls a method on that same class and re-renders the page.
No API endpoints, no JavaScript to write.

If you are used to Laravel tutorials that use controllers, the mental swap is:

| Tutorial Laravel | HealthTrack |
|---|---|
| `PatientController@index` | `App\Livewire\PatientRegistry\Index::render()` |
| `PatientController@store` | a `save()` method on the same class |
| Form request class | a `rules()` method on the same class |
| `redirect()->back()` after POST | Livewire just re-renders; no page reload |

---

## Folder map

```
app/
  Enums/            UserRole, AppointmentStatus
  Http/Middleware/  EnsureUserHasRole -- the "role:midwife" route guard
  Livewire/         Every page, plus the shared ClinicalRecords component
  Models/           Eloquent models
  Policies/         Who may do what to a specific record
  Providers/        AppServiceProvider (policy wiring), FortifyServiceProvider (auth)

config/
  healthtrack.php   *** The centre's details and every clinical record type ***

database/
  factories/        Fake data for tests and the seeder
  migrations/       The schema
  seeders/          DatabaseSeeder -- demo accounts and patients

resources/
  css/app.css       Tailwind theme + the .ht-* component classes
  views/
    auth/           Login, 2FA, password reset (rendered by Fortify)
    components/     Reusable Blade bits, incl. components/layouts/
    livewire/       One Blade file per Livewire component

routes/web.php      Every URL in the app
tests/Feature/      The test suite
```

The two files worth knowing by heart are **`routes/web.php`** (what URLs exist)
and **`config/healthtrack.php`** (what a clinical record is made of).

---

## How a request flows

Take a midwife opening a patient's diagnoses.

```
GET /patients/12?section=diagnoses
        |
        v
routes/web.php
   middleware: auth -> verified -> role:midwife,health_worker
        |
        v
App\Livewire\PatientRegistry\Record
   mount()   route-model binding turns "12" into a Patient
             $this->authorize('view', $patient)   <- PatientPolicy
   render()  returns view('livewire.patient-registry.record')
        |
        v
resources/views/livewire/patient-registry/record.blade.php
   sees section is not "general", so renders:
   <livewire:shared.clinical-records :patient="$patient" type="diagnoses" />
        |
        v
App\Livewire\Shared\ClinicalRecords
   reads config('healthtrack.records.diagnoses')
   queries the Diagnosis model
        |
        v
resources/views/components/layouts/app.blade.php  (the shell)
        |
        v
HTML
```

---

## The three layers of access control

They do different jobs. Use all three.

**1. Route middleware — "may this role see this section at all?"**

```php
Route::middleware('role:midwife')->group(...);
```

Coarse. Returns 403 before any code runs. Defined in `routes/web.php`,
implemented by `app/Http/Middleware/EnsureUserHasRole.php`.

**2. Policies — "may this user touch this particular record?"**

```php
$this->authorize('view', $patient);      // PatientPolicy::view()
$this->authorize('create', Diagnosis::class);  // ClinicalRecordPolicy::create()
```

Fine-grained. Lives in `app/Policies`. **Always call these in `mount()` and in
any method that writes**, even if the route middleware already looks
sufficient — a Livewire method can be invoked directly by a crafted request,
without ever passing through the page that normally exposes it.

**3. The UI — "should we even draw this button?"**

```blade
@if ($this->canManage)
    <button wire:click="toggleForm">Add Diagnosis</button>
@endif
```

Cosmetic only. Never rely on it for security. Hiding a button stops nobody.

---

## Authentication

Laravel Fortify owns login, logout, password reset, email verification and the
two-factor challenge. Configured in `config/fortify.php`, with the Blade views
registered in `app/Providers/FortifyServiceProvider.php`.

Two things to be careful about:

**Do not add a `/login` route to `routes/web.php`.** An earlier version of this
project did. Because its controller called `Auth::attempt()` directly, it
skipped Fortify's two-factor step entirely — accounts with 2FA enabled were
logged straight in.

**Do not re-enable `Features::registration()`.** Public registration is off on
purpose. It was previously on, with a role dropdown on the public form, which
let anyone sign up as a midwife and read every patient record. There is a test
guarding this (`RoleAccessTest`, "does not expose a registration page").

---

## Data model

```
users                         patients
  id                            id
  name                          user_id  ---> users.id  (nullable)
  email                         first_name, middle_name, last_name
  password                      sex, birthdate
  role   (midwife |             contact_number, address
          health_worker |       philhealth_number
          patient)              emergency_contact_*
  two_factor_*
                                    ^
                                    | patient_id
                                    |
        +---------------------------+---------------------------+
        |            |            |            |               |
   diagnoses    lab_values   doctor_notes  medical_      medication_
                                           histories      allergies
        |            |            |            |               |
        +------------+------------+------------+---------------+
                     every one has: patient_id, created_by,
                     one date column, timestamps

   appointments
     patient_id ---> patients.id
     midwife_id ---> users.id
     scheduled_at, status, reason, notes
```

Two things to note:

**`Patient` is the patient, not `User`.** A `User` row is only a login. Every
clinical record points at `patients.id`. `patients.user_id` is nullable,
because a health worker registers a walk-in patient with no email address —
they simply get no portal access unless a midwife grants it later.

That nullable column is where the study's two-step account flow lives. A health
worker creates the patient record (`user_id = null`); a midwife later creates
the login from the *Portal account* panel on the record screen, which fills the
column in. The DFD is explicit that these are different people's jobs, so the
two controls sit on different screens and are governed by different policy
abilities — `PatientPolicy::register()` and `PatientPolicy::createAccount()`.

**There is no `barangay_id` anywhere.** HealthTrack serves one health centre.
Its name and location come from `config/healthtrack.php`. An earlier version
carried a `barangay_id` on every table and scoped every query by it; that is
gone, and reintroducing it would be a significant change, not a small one.

---

## Where the five clinical record types come from

They are **not** hard-coded in five places. `config/healthtrack.php` declares
each one — its model, its label, its date column, and each field with its
validation rules. One component, `App\Livewire\Shared\ClinicalRecords`, reads that
config and renders the table and the form for whichever type it is given.

This is why the midwife's record screen and the patient's read-only portal
cannot drift apart: they are the same component, one of them passed
`:read-only="true"`.

See [03-adding-a-record-type.md](03-adding-a-record-type.md).
