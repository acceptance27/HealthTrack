<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $barangayId = Auth::user()->barangay_id;
        $items = InventoryItem::where('barangay_id', $barangayId)->paginate(20);

        return view('livewire.midwife.inventory.index', compact('items'));
    }

    public function vaccines()
    {
        $barangayId = Auth::user()->barangay_id;
        $vaccines = InventoryItem::where('barangay_id', $barangayId)
            ->where('type', 'vaccine')
            ->paginate(20);

        return view('midwife.inventory.vaccines', compact('vaccines'));
    }

    public function medicines()
    {
        $barangayId = Auth::user()->barangay_id;
        $medicines = InventoryItem::where('barangay_id', $barangayId)
            ->where('type', 'medicine')
            ->paginate(20);

        return view('midwife.inventory.medicines', compact('medicines'));
    }
}
