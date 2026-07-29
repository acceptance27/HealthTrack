{{--
    Layout for the signed-out pages: login, two-factor challenge, password
    reset, email verification. Fortify renders these -- see FortifyServiceProvider.
--}}
@php $centre = config('healthtrack.centre'); @endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'HealthTrack' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="ht-auth-shell">
        <div class="w-full max-w-[420px]">
            <div class="mb-5 text-center">
                <div class="ht-brand justify-center">
                    <span class="ht-brand-mark">HT</span>
                    <span>HealthTrack</span>
                </div>
                <p class="ht-muted mt-2 text-sm">{{ $centre['name'] }}</p>
            </div>

            <div class="ht-auth-card">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
