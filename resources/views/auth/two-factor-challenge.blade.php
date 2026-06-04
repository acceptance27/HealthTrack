<x-app-layout>
    <form method="POST" action="{{ route('two-factor.login') }}" class="mx-auto max-w-md space-y-4 rounded-lg border border-slate-200 bg-white p-6">
        @csrf
        <h1 class="text-xl font-semibold">Two-factor authentication</h1>
        <input name="code" placeholder="Authentication code" class="w-full rounded-md border-slate-300">
        <input name="recovery_code" placeholder="Recovery code" class="w-full rounded-md border-slate-300">
        <x-primary-button type="submit">Continue</x-primary-button>
    </form>
</x-app-layout>
