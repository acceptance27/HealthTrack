<?php

namespace App\Livewire\Midwife\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $sortBy = 'name';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $barangayId = auth()->user()->barangay_id;

        $query = InventoryItem::forBarangay($barangayId)
            ->when(trim($this->search), function ($query) {
                $search = trim($this->search);
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($this->type, fn ($query) => $query->where('type', $this->type));

        if ($this->sortBy === 'stock_low') {
            $query->orderBy('quantity_on_hand', 'asc');
        } elseif ($this->sortBy === 'stock_high') {
            $query->orderBy('quantity_on_hand', 'desc');
        } elseif ($this->sortBy === 'recently_administered') {
            $query->orderBy('updated_at', 'desc');
        } elseif ($this->sortBy === 'expiry_asc') {
            $query->orderBy('expires_at', 'asc');
        } else {
            $query->orderBy('name');
        }

        return view('livewire.midwife.inventory.inventory-table', [
            'items' => $query->paginate(10),
        ]);
    }
}
