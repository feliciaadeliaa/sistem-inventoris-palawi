<?php
// app/Http/Controllers/Admin/ItemController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        Item::create($request->validated());

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
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

    public function showQr(Item $item)
    {
        return response(
            QrCode::format('svg')->size(300)->generate($item->item_id)
        )->header('Content-Type', 'image/svg+xml');
    }

    public function downloadQr(Item $item)
    {
        $qr = QrCode::format('svg')->size(500)->generate($item->item_id);

        return response($qr)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="qr-'.$item->item_id.'.svg"');
    }

    public function printLabels(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:items,id',
        ]);

        $items = Item::whereIn('id', $request->ids)->get();

        return view('admin.items.print-labels', compact('items'));
    }
}