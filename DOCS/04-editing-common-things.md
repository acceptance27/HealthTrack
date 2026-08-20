# 4. Editing common things

A lookup table for "I need to change X — where do I go?"

| I want to... | Go to |
|---|---|
| Change the health centre's name | `config/healthtrack.php` (or the `.env` values) |
| Add/rename a nav link | `resources/views/components/layouts/app.blade.php` |
| Change colours, spacing, fonts | `resources/css/app.css` |
| Add a page | `routes/web.php` + `app/Livewire/` — see [02](02-adding-a-page.md) |
| Add a field to a record | `config/healthtrack.php` — see [03](03-adding-a-record-type.md) |
| Change who may do what | `app/Policies/` |
| Change which role sees a page | `routes/web.php` |
| Change what the login page says | `resources/views/auth/login.blade.php` |
| Change demo data | `database/seeders/DatabaseSeeder.php` |
| Add a patient demographic field | Migration + `Patient` model + registration form |

---

## Changing the look

Everything visual lives in `resources/css/app.css`.

### Colours

At the top of the file:

```css
@theme {
    --color-brand: #0f6b5f;          /* the teal */
    --color-brand-strong: #0c4f46;   /* darker teal for headings and buttons */
    --color-brand-warm: #d0742a;     /* orange accent */
    --color-parchment: #f6f1e8;      /* page background */
    /* ... */
}
```

Change a value here and it updates everywhere. Do not hunt for hex codes in
Blade files.

### Component classes

Below the theme block, in `@layer components`:

| Class | Use for |
|---|---|
| `.ht-panel` | A white card with padding — the standard content box |
| `.ht-card` | The same surface without padding |
| `.ht-page-header` | The title block at the top of a page (via `<x-page-header>`) |
| `.ht-metric` / `.ht-metric-grid` | Dashboard number tiles |
| `.ht-table` / `.ht-table-scroll` | Data tables. Always wrap in the scroll div |
| `.ht-input` / `.ht-field` | Form controls and their labels |
| `.ht-button` / `.ht-button-muted` / `.ht-button-danger` | Buttons |
| `.ht-pill` | Small rounded badge |
| `.ht-empty` | "Nothing here yet" placeholder |
| `.ht-muted` | Secondary text colour |

Use these for anything repeated; use plain Tailwind utilities (`grid gap-4`,
`text-sm`) for one-off layout.

> **Do not put `<style>` blocks in Blade files.** The previous version had the
> same 200 lines of CSS pasted into three templates and they had already
> drifted apart. If you need a new repeated style, add a `.ht-*` class.

### Tailwind v4

There is no `tailwind.config.js` — v4 is configured in CSS. The `@theme` block
*is* the config. `vite.config.js` loads it via the `@tailwindcss/vite` plugin.

---

## Changing the navigation

`resources/views/components/layouts/app.blade.php`. One branch per role:

```blade
@if ($user->isMidwife())
    <a href="{{ route('midwife.dashboard') }}"
       @if(request()->routeIs('midwife.dashboard')) aria-current="page" @endif>Dashboard</a>
    ...
@elseif ($user->isHealthWorker())
    ...
@else
    {{-- patient --}}
@endif
```

The active highlight comes from `aria-current="page"`, not a CSS class.
`request()->routeIs()` accepts wildcards: `routeIs('patients.*')` matches both
the list and the record screen.

---

## Changing permissions

### "Health workers should be able to record allergies"

`app/Policies/ClinicalRecordPolicy.php`:

```php
public function create(User $user): bool
{
    return $user->isMidwife();          // before
    return $user->isStaff();            // after -- midwife or health worker
}
```

That one line changes it everywhere: the "Add" button appears for health
workers and the server-side check now permits them.

If only *allergies* should change, give that model its own policy class
(`MedicationAllergyPolicy`) and remove it from the loop in
`AppServiceProvider::boot()`.

### "Patients should be able to request appointments"

This reverses a deliberate decision — the study describes patient access as
read-only (Figure 7). If you do want it:

1. `AppointmentPolicy::create()` — allow patients.
2. Add a route in the `role:patient` group.
3. Build a Livewire component for the request form.
4. Update `tests/Feature/PatientPortalTest.php`, which currently asserts the
   opposite.
5. **Update the paper**, or the code and the document will disagree again.

---

## Adding a role

1. Add the case to `app/Enums/UserRole.php`, plus its `label()` and
   `homeRoute()` entries.
2. Add a `is<Role>()` helper on `app/Models/User.php`.
3. Add a route group: `Route::middleware('role:new_role')`.
4. Add a nav branch in the app layout.
5. Update the policies that ask `isStaff()`.
6. Add a factory state in `database/factories/UserFactory.php`.
7. Add tests in `tests/Feature/RoleAccessTest.php`.

Step 5 is the one people forget. `isStaff()` currently means "not a patient" —
check that is still what you want.

---

## Adding a patient demographic field

Say patients need an occupation.

1. **Migration:**
   ```php
   Schema::table('patients', fn (Blueprint $t) => $t->string('occupation')->nullable());
   ```
2. **`$fillable`** in `app/Models/Patient.php`.
3. **Registration form** — add a property and rule to
   `app/Livewire/HealthWorker/RegisterPatient.php`, and a field to its Blade file:
   ```blade
   <x-field name="occupation" label="Occupation" />
   ```
4. **Record screen** — add it to the `@foreach` in
   `resources/views/livewire/patient-registry/record.blade.php`:
   ```php
   'Occupation' => $patient->occupation,
   ```

Patient demographics are *not* config-driven — only clinical records are. This
is deliberate: demographics are a fixed form, clinical records are the part
that grows.

---

## Changing validation

**Clinical records:** `config/healthtrack.php`, the `rules` key.

**Patient registration:** the `rules()` method in
`app/Livewire/HealthWorker/RegisterPatient.php`.

**Appointments:** inline in `scheduleAppointment()` in
`app/Livewire/Patients/Record.php`.

To make an error message read better, add a `validationAttributes()` method:

```php
protected function validationAttributes(): array
{
    return ['scheduledAt' => 'date and time'];
}
```

Turns "The scheduled at field is required" into "The date and time field is
required".

---

## Changing the seeded demo data

`database/seeders/DatabaseSeeder.php`. To reset:

```bash
php artisan migrate:fresh --seed
```

That **drops every table** and rebuilds. Never run it against real data.

---

## Useful commands

```bash
php artisan route:list
```

```bash
php artisan healthtrack:about
```

```bash
php artisan test
```

```bash
php artisan migrate:fresh --seed
```

```bash
./vendor/bin/pint
```

`route:list` shows every URL and its middleware — the fastest way to confirm a
new page is wired correctly. `pint` formats PHP to the project's style.
