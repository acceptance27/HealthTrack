<?php

namespace App\Livewire\Midwife\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;

class InventoryForm extends Component
{
    public string $name = '';
    public string $type = 'medicine';
    public string $unit = '';
    public int $quantity_on_hand = 0;
    public int $reorder_level = 0;
    public ?string $expires_at = null;

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:vaccine,medicine'],
            'unit' => ['required', 'string', 'max:50'],
            'quantity_on_hand' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'expires_at' => ['nullable', 'date'],
        ]);

        InventoryItem::create($data + ['barangay_id' => auth()->user()->barangay_id]);

        $this->reset(['name', 'unit', 'quantity_on_hand', 'reorder_level', 'expires_at']);
        $this->type = 'medicine';
        $this->dispatch('inventory-item-created');
    }

    public function render()
    {
        return view('livewire.midwife.inventory.inventory-form');
    }
}
