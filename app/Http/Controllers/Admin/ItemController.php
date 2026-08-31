<?php
// app/Http/Controllers/Admin/ItemController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['category', 'location'])
            ->latest()
            ->paginate(15);

        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $locations = Location::orderBy('nama_lokasi')->get();

        return view('admin.items.create', compact('categories', 'locations'));
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
        ->route('barang.index')
        ->with('success', 'Barang berhasil ditambahkan dan tercatat sebagai Stock In.');
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $locations = Location::orderBy('nama_lokasi')->get();

        return view('admin.items.edit', compact('item', 'categories', 'locations'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->validated());

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        // Soft-status, bukan hapus (sesuai Modul 2)
        $item->update(['is_active' => false, 'status' => 'nonaktif']);

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil dinonaktifkan.');
    }
}