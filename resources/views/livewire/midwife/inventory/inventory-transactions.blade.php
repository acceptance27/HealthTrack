<div class="space-y-4">
    <h1 class="text-2xl font-semibold">{{ $item->name }} Transactions</h1>
    <form wire:submit="save" class="grid gap-3 rounded-lg border border-slate-200 bg-white p-4 md:grid-cols-[160px_120px_1fr_auto]">
        <select wire:model="type" class="rounded-md border-slate-300">
            <option value="stock_in">Stock In</option>
            <option value="stock_out">Stock Out</option>
            <option value="adjustment">Adjustment</option>
        </select>
        <input wire:model="quantity" type="number" min="1" class="rounded-md border-slate-300">
        <input wire:model="remarks" placeholder="Remarks" class="rounded-md border-slate-300">
        <x-primary-button type="submit">Save</x-primary-button>
    </form>
</div>
