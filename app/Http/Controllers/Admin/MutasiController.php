<?php
// app/Http/Controllers/Admin/MutasiController.php
namespace App\Http\Controllers\Admin;

use App\Exports\MutasiExport;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Location;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::orderBy('nama_lokasi')->get();
        $categories = \App\Models\Category::orderBy('nama_kategori')->get();
        $golonganOptions = Item::distinct()->pluck('golongan_at')->filter()->sort()->values();

        $selectedItem = null;
        if ($request->filled('item_id')) {
            $selectedItem = Item::with('location')
                ->where('item_id', $request->item_id)
                ->where('is_active', true)
                ->first();
        }

        $riwayatQuery = Transaction::with(['item', 'lokasiAsal', 'lokasiTujuan'])
            ->where('jenis_transaksi', 'Mutasi');

        if ($request->filled('date_from')) {
            $riwayatQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $riwayatQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'desc');
        $riwayat = $riwayatQuery->orderBy('created_at', $sort)->paginate(10)->withQueryString();

        return view('admin.mutasi.index', compact(
            'locations', 'categories', 'golonganOptions', 'selectedItem', 'riwayat'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,item_id'],
            'lokasi_tujuan_id' => ['required', 'exists:locations,id'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $item = Item::where('item_id', $validated['item_id'])->firstOrFail();

        if ($item->location_id == $validated['lokasi_tujuan_id']) {
            return back()->with('error', 'Lokasi tujuan sama dengan lokasi saat ini.')->withInput();
        }

        Transaction::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'jenis_transaksi' => 'Mutasi',
            'status' => 'menunggu_approval',
            'keterangan' => $validated['keterangan'] ?? null,
            'lokasi_asal_id' => $item->location_id,
            'lokasi_tujuan_id' => $validated['lokasi_tujuan_id'],
        ]);

        return redirect()
            ->route('admin.transaksi.mutasi.index')
            ->with('success', 'Mutasi lokasi berhasil diajukan, menunggu approval GM.');
    }

    public function export(Request $request)
    {
        $ids = array_filter(explode(',', $request->get('selected_ids', '')));
        $filters = $request->only(['date_from', 'date_to', 'sort']);

        return Excel::download(
            new MutasiExport($filters, $ids),
            'mutasi-' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}