{{--
    Session flash messages. Rendered once in layouts/app.blade.php, so any
    page can just call session()->flash('status', '...') or, from a Livewire
    component, session()->flash(...) followed by a redirect.
--}}
@if (session('status'))
    <div class="ht-panel" style="border-left: 4px solid var(--color-brand);">
        <p class="m-0 text-sm font-bold" style="color: var(--color-brand-strong);">
            {{ session('status') }}
        </p>
    </div>
@endif

@if (session('error'))
    <div class="ht-panel" style="border-left: 4px solid var(--color-danger);">
        <p class="m-0 text-sm font-bold" style="color: var(--color-danger);">
            {{ session('error') }}
        </p>
    </div>
@endif
