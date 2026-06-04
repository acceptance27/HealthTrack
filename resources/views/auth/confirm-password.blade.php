<x-app-layout>
    <form method="POST" action="{{ route('password.confirm') }}" class="mx-auto max-w-md space-y-4 rounded-lg border border-slate-200 bg-white p-6">
        @csrf
        <h1 class="text-xl font-semibold">Confirm password</h1>
        <input name="password" type="password" required class="w-full rounded-md border-slate-300">
        <x-primary-button type="submit">Confirm</x-primary-button>
    </form>
</x-app-layout>
