<x-app-layout>
    <form method="POST" action="{{ route('password.email') }}" class="mx-auto max-w-md space-y-4 rounded-lg border border-slate-200 bg-white p-6">
        @csrf
        <h1 class="text-xl font-semibold">Reset password</h1>
        <input name="email" type="email" placeholder="Email" required class="w-full rounded-md border-slate-300">
        <x-primary-button type="submit">Send reset link</x-primary-button>
    </form>
</x-app-layout>
