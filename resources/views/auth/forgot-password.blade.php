<x-layouts.guest title="Forgot password">
    <h1 class="mb-1 text-xl font-bold">Forgot your password?</h1>
    <p class="ht-muted mb-4 text-sm">
        Enter your email address and we will send you a link to set a new password.
    </p>

    @if (session('status'))
        <div class="mb-4 rounded-xl p-3 text-sm"
             style="background: rgba(15, 107, 95, 0.08); color: var(--color-brand-strong);">
            {{ session('status') }}
        </div>
    @endif

    @error('email')
        <div class="mb-4 rounded-xl p-3 text-sm"
             style="background: rgba(178, 69, 62, 0.08); color: var(--color-danger);">
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('password.email') }}" class="grid gap-3">
        @csrf

        <label class="ht-field">
            <span>Email address</span>
            <input type="email" name="email" value="{{ old('email') }}"
                   required autofocus class="ht-input">
        </label>

        <button type="submit" class="ht-button">Email password reset link</button>

        <a href="{{ route('login') }}" class="ht-muted text-center text-xs underline">
            Back to sign in
        </a>
    </form>
</x-layouts.guest>
