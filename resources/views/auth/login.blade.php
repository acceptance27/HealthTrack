{{-- Rendered by Fortify. See App\Providers\FortifyServiceProvider. --}}
<x-layouts.guest title="Sign in">
    <h1 class="mb-1 text-xl font-bold">HealthTrack Sign in</h1>
    <p class="ht-muted mb-4 text-sm">Use the account issued by the health centre.</p>

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

    @if (session('status'))
        <div class="mb-4 rounded-xl p-3 text-sm"
             style="background: rgba(15, 107, 95, 0.08); color: var(--color-brand-strong);">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="grid gap-3">
        @csrf

        <label class="ht-field">
            <span>Email address</span>
            <input type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username" class="ht-input">
        </label>

        <label class="ht-field">
            <span>Password</span>
            <input type="password" name="password" required
                   autocomplete="current-password" class="ht-input">
        </label>

        <label class="flex items-center gap-2 text-sm font-normal">
            <input type="checkbox" name="remember">
            Remember me
        </label>

        <button type="submit" class="ht-button">Sign in</button>

        <a href="{{ route('password.request') }}"
           class="ht-muted text-center text-xs underline">Forgot your password?</a>
    </form>
</x-layouts.guest>
