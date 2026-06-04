<?php

namespace App\Livewire\Midwife\Inventory;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class InventoryTransactions extends Component
{
    use AuthorizesRequests;

    public InventoryItem $item;
    public string $type = 'stock_in';
    public int $quantity = 1;
    public string $remarks = '';

    public function mount(InventoryItem $item): void
    {
        $this->authorize('view', $item);
        $this->item = $item;
    }

    public function save(): void
    {
        $data = $this->validate([
            'type' => ['required', 'in:stock_in,stock_out,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        InventoryTransaction::create($data + [
            'barangay_id' => $this->item->barangay_id,
            'inventory_item_id' => $this->item->id,
            'created_by' => auth()->id(),
        ]);

        if ($data['type'] === 'stock_out') {
            $this->item->decrement('quantity_on_hand', $data['quantity']);
        } else {
            $this->item->increment('quantity_on_hand', $data['quantity']);
        }

        $this->reset(['remarks']);
    }

    public function render()
    {
        return view('livewire.midwife.inventory.inventory-transactions', [
            'transactions' => $this->item->transactions()->latest()->paginate(10),
        ]);
    }
}
