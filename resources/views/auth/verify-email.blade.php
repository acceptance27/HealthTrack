<x-layouts.guest title="Verify email">
    <h1 class="mb-1 text-xl font-bold">Verify your email address</h1>
    <p class="ht-muted mb-4 text-sm">
        We sent a verification link to your email. Click it to activate your account.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="mb-4 rounded-xl p-3 text-sm"
             style="background: rgba(15, 107, 95, 0.08); color: var(--color-brand-strong);">
            A new verification link has been sent.
        </div>
    @endif

    <div class="grid gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="ht-button w-full">Resend verification email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="ht-button ht-button-muted w-full">Sign out</button>
        </form>
    </div>
</x-layouts.guest>
