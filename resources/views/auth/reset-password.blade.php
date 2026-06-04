<x-app-layout>
    <form method="POST" action="{{ route('password.update') }}" class="mx-auto max-w-md space-y-4 rounded-lg border border-slate-200 bg-white p-6">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">
        <h1 class="text-xl font-semibold">Choose new password</h1>
        <input name="email" type="email" value="{{ request('email') }}" required class="w-full rounded-md border-slate-300">
        <input name="password" type="password" placeholder="Password" required class="w-full rounded-md border-slate-300">
        <input name="password_confirmation" type="password" placeholder="Confirm password" required class="w-full rounded-md border-slate-300">
        <x-primary-button type="submit">Reset password</x-primary-button>
    </form>
</x-app-layout>
