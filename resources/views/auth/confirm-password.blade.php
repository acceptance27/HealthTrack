<x-layouts.guest title="Confirm password">
    <h1 class="mb-1 text-xl font-bold">Confirm your password</h1>
    <p class="ht-muted mb-4 text-sm">
        Please confirm your password before continuing.
    </p>

    @error('password')
        <div class="mb-4 rounded-xl p-3 text-sm"
             style="background: rgba(178, 69, 62, 0.08); color: var(--color-danger);">
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('password.confirm') }}" class="grid gap-3">
        @csrf

        <label class="ht-field">
            <span>Password</span>
            <input type="password" name="password" required autofocus
                   autocomplete="current-password" class="ht-input">
        </label>

        <button type="submit" class="ht-button">Confirm</button>
    </form>
</x-layouts.guest>
