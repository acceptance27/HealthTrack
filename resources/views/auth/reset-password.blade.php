<x-layouts.guest title="Reset password">
    <h1 class="mb-1 text-xl font-bold">Set a new password</h1>
    <p class="ht-muted mb-4 text-sm">Choose a password of at least eight characters.</p>

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

    <form method="POST" action="{{ route('password.update') }}" class="grid gap-3">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <label class="ht-field">
            <span>Email address</span>
            <input type="email" name="email"
                   value="{{ old('email', $request->email) }}"
                   required autofocus class="ht-input">
        </label>

        <label class="ht-field">
            <span>New password</span>
            <input type="password" name="password" required
                   autocomplete="new-password" class="ht-input">
        </label>

        <label class="ht-field">
            <span>Confirm new password</span>
            <input type="password" name="password_confirmation" required
                   autocomplete="new-password" class="ht-input">
        </label>

        <button type="submit" class="ht-button">Reset password</button>
    </form>
</x-layouts.guest>
