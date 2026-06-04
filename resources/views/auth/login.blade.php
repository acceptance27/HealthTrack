<x-app-layout>
    <form method="POST" action="{{ route('login') }}" class="mx-auto max-w-md space-y-4 rounded-lg border border-slate-200 bg-white p-6">
        @csrf
        <h1 class="text-xl font-semibold">Sign in</h1>
        
        @if ($errors->any())
            <div class="text-red-600 text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <input name="email" type="email" placeholder="Email" value="{{ old('email') }}" required autofocus class="w-full rounded-md border-slate-300">
        <input name="password" type="password" placeholder="Password" required class="w-full rounded-md border-slate-300">
        <label class="flex items-center gap-2 text-sm">
            <input name="remember" type="checkbox" class="rounded border-slate-300">
            Remember me
        </label>
        <x-primary-button type="submit">Login</x-primary-button>
    </form>
</x-app-layout>
