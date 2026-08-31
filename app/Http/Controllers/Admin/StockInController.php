<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Transaction;

class StockInController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['item', 'user'])
            ->where('jenis_transaksi', 'Stock In')
            ->latest()
            ->paginate(15);

        return view('admin.stock-in.index', compact('transactions'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $locations = Location::orderBy('nama_lokasi')->get();

        return view('admin.stock-in.create', compact('categories', 'locations'));
    }

    public function store(StoreItemRequest $request)
    {
        $item = Item::create($request->validated());

        $item->transactions()->create([
            'user_id' => auth()->id(),
            'jenis_transaksi' => 'Stock In',
            'keterangan' => 'Penerimaan barang baru',
        ]);

        return redirect()
            ->route('stock-in.index')
            ->with('success', 'Barang berhasil ditambahkan dan tercatat sebagai Stock In.');
    }
}