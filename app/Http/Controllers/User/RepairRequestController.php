<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;

class RepairRequestController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with('item')
            ->where('jenis_transaksi', 'permintaan_perbaikan')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);
 
        return view('users.repair.index', compact('transactions'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string|exists:items,item_id',
        ]);

        $item = Item::where('item_id', $request->query('item_id'))->firstOrFail();

        return view('users.repair.create', compact('item'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|string|exists:items,item_id',
            'keterangan' => 'required|string|max:1000', // deskripsi kerusakan
        ]);

        $item = Item::where('item_id', $validated['item_id'])->firstOrFail();

        Transaction::create([
            'item_id' => $item->id, // primary key items, bukan kode
            'user_id' => $request->user()->id,
            'jenis_transaksi' => 'permintaan_perbaikan',
            'status' => 'menunggu_approval',
            'keterangan' => $validated['keterangan'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Permintaan perbaikan berhasil diajukan.');
    }
}