# 2. Adding a new page

A worked example: an **Immunization Schedule** page for the midwife, at
`/midwife/immunizations`.

Four steps. Nothing else needs touching.

---

## Step 1 — Create the Livewire class

`app/Livewire/Midwife/Immunizations.php`

```php
<?php

namespace App\Livewire\Midwife;

use App\Models\Patient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Immunization Schedule')]
class Immunizations extends Component
{
    public function render()
    {
        return view('livewire.midwife.immunizations', [
            'children' => Patient::where('birthdate', '>=', now()->subYears(5))
                ->orderBy('birthdate')
                ->get(),
        ]);
    }
}
```

- `#[Layout(...)]` wraps the page in the application shell (top bar, nav, flash
  messages). Every full-page component needs it.
- `#[Title(...)]` sets the browser tab title.
- Anything in the array passed to `view()` becomes a variable in the Blade file.

---

## Step 2 — Create the Blade file

The path mirrors the component name: `Midwife\Immunizations` →
`resources/views/livewire/midwife/immunizations.blade.php`

```blade
<div class="grid gap-4">
    <x-page-header
        title="Immunization Schedule"
        subtitle="Children under five registered at the centre."
    >
        <x-slot:aside>
            <span class="ht-pill">{{ $children->count() }} children</span>
        </x-slot:aside>
    </x-page-header>

    <div class="ht-panel">
        <h2>Due for immunization</h2>

        @if ($children->isEmpty())
            <div class="ht-empty">No children under five are registered.</div>
        @else
            <div class="ht-table-scroll">
                <table class="ht-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Date of birth</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($children as $child)
                            <tr>
                                <td class="font-bold" style="color: var(--color-brand-strong);">
                                    {{ $child->fullName() }}
                                </td>
                                <td>{{ $child->age() }}</td>
                                <td>{{ $child->birthdate->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
```

> **A Livewire Blade file must have exactly one root element.** The outer
> `<div>` is required. Two sibling elements at the top level is the single most
> common Livewire error — see [06-troubleshooting.md](06-troubleshooting.md).

---

## Step 3 — Add the route

In `routes/web.php`, add the import at the top:

```php
use App\Livewire\Midwife\Immunizations;
```

and the route inside the midwife group:

```php
Route::middleware('role:midwife')
    ->prefix('midwife')
    ->name('midwife.')
    ->group(function (): void {
        Route::get('/dashboard', MidwifeDashboard::class)->name('dashboard');
        Route::get('/appointments', Appointments::class)->name('appointments');
        Route::get('/immunizations', Immunizations::class)->name('immunizations');  // new
    });
```

The URL is now `/midwife/immunizations` and the route name is
`midwife.immunizations`. Putting it in the `role:midwife` group is what stops
health workers and patients from opening it.

---

## Step 4 — Add it to the navigation

In `resources/views/components/layouts/app.blade.php`, find the midwife branch
and add a link:

```blade
@if ($user->isMidwife())
    <a href="{{ route('midwife.dashboard') }}" ...>Dashboard</a>
    <a href="{{ route('patients.index') }}" ...>Patients</a>
    <a href="{{ route('midwife.appointments') }}" ...>Appointments</a>
    <a href="{{ route('midwife.immunizations') }}"
       @if(request()->routeIs('midwife.immunizations')) aria-current="page" @endif>Immunizations</a>
@elseif ...
```

The `aria-current="page"` is what highlights the active tab — the styling hangs
off that attribute, not a CSS class.

---

## Done

Visit `/midwife/immunizations`. If `npm run dev` is running, changes to the
Blade file appear on save.

---

## Making the page interactive

Add a public property and a method to the class; bind to them in Blade.

```php
class Immunizations extends Component
{
    public string $search = '';          // public = available in Blade

    public function markAsDone(int $id): void
    {
        // ... called by wire:click="markAsDone(5)"
    }
}
```

```blade
<input wire:model.live="search" class="ht-input">

<button wire:click="markAsDone({{ $child->id }})" class="ht-button">Done</button>
```

- `wire:model.live` — updates the server on every keystroke. Use
  `wire:model.live.debounce.300ms` for search boxes so you are not firing a
  request per character.
- `wire:model` (without `.live`) — only syncs when the form is submitted.
  Cheaper; use it for normal form fields.
- `wire:click="method"` — calls the method and re-renders.
- `wire:submit="method"` — on a `<form>`, replaces the normal POST.

---

## If the page writes data

Two rules, both non-negotiable.

**Authorize in `mount()`:**

```php
public function mount(): void
{
    $this->authorize('viewAny', Patient::class);
}
```

**Authorize again in every method that writes:**

```php
public function markAsDone(int $id): void
{
    $this->authorize('create', Immunization::class);
    // ...
}
```

The second is not redundant. Livewire methods are reachable by a crafted
request without passing through the page, so a check that only runs in
`mount()` protects nothing.

---

## Add a test

`tests/Feature/ImmunizationTest.php`

```php
use App\Livewire\Midwife\Immunizations;
use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

it('lists children under five', function () {
    $midwife = User::factory()->midwife()->create();
    Patient::factory()->create(['last_name' => 'Santos', 'birthdate' => now()->subYears(2)]);
    Patient::factory()->create(['last_name' => 'Rizal', 'birthdate' => now()->subYears(40)]);

    Livewire::actingAs($midwife)
        ->test(Immunizations::class)
        ->assertSee('Santos')
        ->assertDontSee('Rizal');
});

it('is closed to health workers', function () {
    $this->actingAs(User::factory()->healthWorker()->create())
        ->get('/midwife/immunizations')
        ->assertForbidden();
});
```

```bash
php artisan test
```
