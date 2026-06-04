<form wire:submit="save" class="grid gap-3 rounded-lg border border-slate-200 bg-white p-4 md:grid-cols-[220px_1fr_auto]">
    <div>
        <input wire:model="scheduled_at" type="datetime-local" class="w-full rounded-md border-slate-300">
        <x-form-error name="scheduled_at" />
    </div>
    <div>
        <input wire:model="reason" type="text" placeholder="Reason for appointment" class="w-full rounded-md border-slate-300">
        <x-form-error name="reason" />
    </div>
    <x-primary-button type="submit">Request</x-primary-button>
</form>
