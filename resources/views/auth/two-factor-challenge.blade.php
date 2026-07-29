{{--
    Shown after a correct password when the account has two-factor enabled.
    Fortify handles this step -- it only works because login goes through
    Fortify rather than a hand-written controller.

    Both fields are shown at once and either one is accepted, so this page
    needs no JavaScript.
--}}
<x-layouts.guest title="Two-factor authentication">
    <h1 class="mb-1 text-xl font-bold">Two-factor authentication</h1>
    <p class="ht-muted mb-4 text-sm">
        Enter the six-digit code from your authenticator app.
    </p>

    @if ($errors->any())
        <div class="mb-4 rounded-xl p-3 text-sm"
             style="background: rgba(178, 69, 62, 0.08); color: var(--color-danger);">
            <ul class="m-0 list-none p-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.login') }}" class="grid gap-3">
        @csrf

        <label class="ht-field">
            <span>Authentication code</span>
            <input type="text" name="code" inputmode="numeric" autofocus
                   autocomplete="one-time-code" class="ht-input">
        </label>

        <div class="ht-muted my-1 text-center text-xs">
            -- or, if you have lost your device --
        </div>

        <label class="ht-field">
            <span>Recovery code</span>
            <input type="text" name="recovery_code" autocomplete="one-time-code" class="ht-input">
        </label>

        <button type="submit" class="ht-button">Continue</button>
    </form>
</x-layouts.guest>
