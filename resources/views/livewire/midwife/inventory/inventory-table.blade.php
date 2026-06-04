<div class="inventory-table-container">
    <div class="page-heading">
        <div class="flex items-baseline gap-3">
            <h1>Inventory</h1>
            <span class="mw-muted" style="font-size: 14px; font-weight: 500;">{{ $items->total() }} Items Tracked</span>
        </div>
    </div>

    <div class="card">
        <div class="mw-filter-bar">
                <div class="mw-filter-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mw-muted"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search inventory...">
                </div>
                <div class="mw-filter-item">
                    <label>Type</label>
                    <select wire:model.live="type" class="mw-filter-select">
                        <option value="">All</option>
                        <option value="vaccine">Vaccines</option>
                        <option value="medicine">Medicines</option>
                    </select>
                </div>
                <div class="mw-filter-item">
                    <label>Sort</label>
                    <select wire:model.live="sortBy" class="mw-filter-select">
                        <option value="name">Name (A-Z)</option>
                        <option value="stock_low">Stock (Low first)</option>
                        <option value="stock_high">Stock (High first)</option>
                        <option value="expiry_asc">Expiry (Soonest)</option>
                        <option value="recently_administered">Recently Updated</option>
                    </select>
                </div>
            </div>
        <div class="overflow-x-auto">
            <table class="mw-status-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Stock</th>
                        <th>Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="font-bold">{{ $item->name }}</td>
                            <td>{{ ucfirst($item->type->value) }}</td>
                            <td class="{{ $item->quantity_on_hand <= $item->reorder_level ? 'text-red-600 font-bold' : '' }}">
                                {{ $item->quantity_on_hand }} {{ $item->unit }}
                            </td>
                            <td>{{ $item->expires_at?->format('M d, Y') ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center mw-muted">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="mt-6">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    <livewire:midwife.inventory.inventory-form />
</div>
