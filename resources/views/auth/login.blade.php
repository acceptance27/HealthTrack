{{-- Rendered by Fortify. See App\Providers\FortifyServiceProvider. --}}
<x-layouts.guest title="Sign in">
    <x-slot name="footer">
        © 2026 HealthTrack. All rights reserved.
    </x-slot>

    <div class="ht-login-panel">
        <div class="ht-login-header">
            <h1>Welcome to HealthTrack</h1>
            <p>Sign in to access your account.</p>
        </div>

        <div class="ht-login-divider"></div>

        <div class="ht-select-account">
            <p>Select your account type</p>
            <div class="ht-account-grid">
                <button type="button" class="ht-account-card is-selected" aria-pressed="true">
                    <span class="ht-account-icon" aria-hidden="true">
                        <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="40" cy="23" r="11"></circle>
                            <path d="M24 60c2.5-10 10.3-15 16-15s13.5 5 16 15"></path>
                            <path d="M34 28c2.2 2 5.8 2 8 0"></path>
                        </svg>
                    </span>
                    <span>Patient</span>
                </button>
                <button type="button" class="ht-account-card" aria-pressed="false">
                    <span class="ht-account-icon" aria-hidden="true">
                        <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M32 29c0-7 4.5-12 8-12s8 5 8 12v3c0 7-4.5 12-8 12s-8-5-8-12z"></path>
                            <path d="M25 59c2.7-9.8 9.8-14.5 15-14.5s12.3 4.7 15 14.5"></path>
                            <path d="M40 16v16"></path>
                            <path d="M31 28h18"></path>
                            <path d="M55 43h10"></path>
                            <path d="M60 38v10"></path>
                            <path d="M15 43h10"></path>
                            <path d="M20 38v10"></path>
                        </svg>
                    </span>
                    <span>Midwife /<br>Administrator</span>
                </button>
                <button type="button" class="ht-account-card" aria-pressed="false">
                    <span class="ht-account-icon" aria-hidden="true">
                        <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="40" cy="23" r="11"></circle>
                            <path d="M22 59c2.2-10.5 10.8-16 18-16s15.8 5.5 18 16"></path>
                            <path d="M57 42h8"></path>
                            <path d="M61 38v8"></path>
                            <path d="M28 42h8"></path>
                            <path d="M32 38v8"></path>
                        </svg>
                    </span>
                    <span>Health Worker</span>
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="ht-login-alert ht-login-alert-error">
                <ul class="m-0 list-none p-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="ht-login-alert ht-login-alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="ht-login-form">
            @csrf

            <label class="ht-login-field">
                <span>Email Address</span>
                <div class="ht-input-wrap">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5h16v9H4z"/><path d="m5 8 7 6 7-6"/></svg>
                    <input type="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="Enter your email address" class="ht-input">
                </div>
            </label>

            <label class="ht-login-field">
                <span>Password</span>
                <div class="ht-input-wrap">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10V8a5 5 0 1 1 10 0v2"/><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M12 14v2"/></svg>
                    <input type="password" name="password" id="password" required
                           autocomplete="current-password" placeholder="Enter your password" class="ht-input ht-password-input">
                    <button type="button" class="ht-password-toggle" aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </label>

            <label class="sr-only" for="remember">
                <input id="remember" type="checkbox" name="remember">
                Remember me
            </label>

            <button type="submit" class="ht-button ht-auth-submit">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10V8a5 5 0 1 1 10 0v2"/><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M12 14v2"/></svg>
                Sign In
            </button>

            <a href="{{ route('password.request') }}" class="ht-login-link">Forgot your password?</a>
        </form>
    </div>
</x-layouts.guest>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.querySelector('.ht-password-toggle');
        const passwordInput = document.querySelector('.ht-password-input');

        if (toggleButton && passwordInput) {
            toggleButton.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleButton.setAttribute('aria-pressed', String(isPassword));
                toggleButton.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
        }
    });
</script>
