# 6. Troubleshooting

Errors you are likely to hit, and what they actually mean.

---

## Setup

### `SQLSTATE[08006] could not connect to server`

PostgreSQL is not running, or `.env` does not match it.

Check the service is up, then verify `DB_HOST`, `DB_PORT`, `DB_USERNAME` and
`DB_PASSWORD`. Confirm the database exists:

```bash
psql -U postgres -l
```

If `healthtrack` is not listed:

```bash
createdb -U postgres healthtrack
```

### `SQLSTATE[3D000] database "healthtrack" does not exist`

Migrations do not create the database, only its tables. Run `createdb` as above.

### `could not find driver`

The PostgreSQL PHP extension is missing. In `php.ini`, uncomment:

```ini
extension=pdo_pgsql
extension=pgsql
```

Restart the server, then confirm:

```bash
php -m
```

`pdo_pgsql` should be in the list.

### `No application encryption key has been specified`

```bash
php artisan key:generate
```

### Pages load but have no styling

Vite is not running, or assets were never built.

```bash
npm run dev
```

For a one-off build instead: `npm run build`. If it still looks unstyled, check
the browser console for a failed request to `localhost:5173`.

---

## Livewire

### `Livewire only supports one HTML element per component`

Your Blade file has two or more top-level elements. Wrap everything in a single
`<div>`.

```blade
{{-- Wrong --}}
<x-page-header title="Patients" />
<div class="ht-panel">...</div>

{{-- Right --}}
<div class="grid gap-4">
    <x-page-header title="Patients" />
    <div class="ht-panel">...</div>
</div>
```

This is the most common Livewire error by a wide margin.

### `Unable to find component: [xyz]`

The class name and the tag do not match. `<livewire:shared.clinical-records />` maps to
`App\Livewire\Shared\ClinicalRecords`. Check the namespace matches the folder, then:

```bash
php artisan optimize:clear
```

### Rows show the wrong data after deleting one

A missing `wire:key` in the loop.

```blade
@foreach ($records as $record)
    <tr wire:key="{{ $type }}-{{ $record->id }}">
```

The key must be unique across the whole page — that is why it includes `$type`.

### A form field saves as blank, with no error

The column is missing from `$fillable` on the model. Laravel's mass-assignment
protection drops it silently.

For clinical records, also confirm the field is declared in
`config/healthtrack.php` — if it is not there, no input is rendered at all.

### `Property [$foo] not found on component`

Blade is using a variable the class does not expose. Either make it a `public`
property, or pass it through the array in `render()`.

Note that `$this->definition` in Blade resolves to the `definition()` method on
the class, which carries Livewire's `#[Computed]` attribute. Computed
properties are readable from Blade; plain methods are not.

---

## Permissions

### 403 on a page you expect to work

The route middleware refused the role. Check which group the route is in:

```bash
php artisan route:list --path=patients
```

The middleware column shows `role:midwife` and similar. A 403 here happens
*before* any policy runs.

### 403 from an action, but the page loads fine

A policy refused it. Look in `app/Policies/`. Remember `ClinicalRecordPolicy`
covers all five record models — the mapping is the loop in
`AppServiceProvider::boot()`.

### `This action is unauthorized` in a test

Usually intended — several tests assert exactly this. If it is a new failure,
check the factory state: `User::factory()->create()` makes a **patient**, not a
midwife. Use `->midwife()` or `->healthWorker()`.

### 404 where you expected 403

Deliberate, in two places. `ClinicalRecords::delete()` and
`Record::deleteAppointment()` scope by patient before `findOrFail()`, so a
record belonging to someone else looks absent rather than forbidden. That is
the safer answer — 403 would confirm the record exists.

---

## Login

### Login succeeds but lands on a blank page or a loop

`UserRole::homeRoute()` names a route that does not exist. Every role in the
enum needs a matching named route in `routes/web.php`.

### The two-factor screen never appears

Something registered a competing `/login` route. Check:

```bash
php artisan route:list --path=login
```

There must be exactly one POST `/login`, belonging to Fortify. If
`routes/web.php` defines one, delete it — a custom controller calling
`Auth::attempt()` bypasses the 2FA challenge entirely. This is the bug the
current structure exists to prevent.

### `/register` returns 404

Correct. Public registration is switched off on purpose — see
`config/fortify.php`. Patient accounts are created by a health worker; staff
accounts by the seeder. There is a test asserting this stays true.

### Password reset emails never arrive

In development `MAIL_MAILER=log`, so they are written to
`storage/logs/laravel.log` instead. Open that file and copy the link out.

---

## Tests

### `Database file at path [:memory:] does not exist`

`phpunit.xml` is not being read, usually because a stale config cache is
overriding it.

```bash
php artisan config:clear
```

### A test passes alone but fails in the suite

Leftover state. Confirm `tests/Pest.php` still applies `RefreshDatabase` to the
`Feature` directory, and that the test does not depend on auto-increment IDs
starting at 1.

### Everything fails with `Class "..." not found`

```bash
composer dump-autoload
```

---

## When nothing makes sense

Clear every cache:

```bash
php artisan optimize:clear
```

That flushes config, routes, views and events. It fixes a surprising number of
"but I changed that" problems — especially after editing
`config/healthtrack.php`, which is cached like any other config file.
