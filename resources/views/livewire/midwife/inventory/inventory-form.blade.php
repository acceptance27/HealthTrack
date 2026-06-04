<form wire:submit="save" class="grid gap-3 rounded-lg border border-slate-200 bg-white p-4 md:grid-cols-6">
    <input wire:model="name" placeholder="Item name" class="rounded-md border-slate-300 md:col-span-2">
    <select wire:model="type" class="rounded-md border-slate-300">
        <option value="medicine">Medicine</option>
        <option value="vaccine">Vaccine</option>
    </select>
    <input wire:model="unit" placeholder="Unit" class="rounded-md border-slate-300">
    <input wire:model="quantity_on_hand" type="number" min="0" class="rounded-md border-slate-300">
    <x-primary-button type="submit">Add</x-primary-button>
</form>
