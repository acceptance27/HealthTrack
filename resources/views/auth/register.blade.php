<x-app-layout>
    <form method="POST" action="{{ route('register') }}" class="mx-auto max-w-md space-y-4 rounded-lg border border-slate-200 bg-white p-6">
        @csrf
        <h1 class="text-xl font-semibold">Create account</h1>
        <input name="name" placeholder="Name" required class="w-full rounded-md border-slate-300">
        <input name="email" type="email" placeholder="Email" required class="w-full rounded-md border-slate-300">
        <input name="barangay_id" type="number" placeholder="Barangay ID" required class="w-full rounded-md border-slate-300">
        <select name="role" class="w-full rounded-md border-slate-300">
            <option value="patient">Patient</option>
            <option value="midwife">Midwife</option>
        </select>
        <input name="password" type="password" placeholder="Password" required class="w-full rounded-md border-slate-300">
        <input name="password_confirmation" type="password" placeholder="Confirm password" required class="w-full rounded-md border-slate-300">
        <x-primary-button type="submit">Register</x-primary-button>
    </form>
</x-app-layout>
