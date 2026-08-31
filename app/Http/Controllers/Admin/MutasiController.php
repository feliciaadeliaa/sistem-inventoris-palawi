<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Location;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::orderBy('nama_lokasi')->get();

        $selectedItem = null;
        if ($request->filled('item_id')) {
            $selectedItem = Item::with('location')
                ->where('item_id', $request->item_id)
                ->where('is_active', true)
                ->first();
        }

        $riwayat = Transaction::with(['item', 'lokasiAsal', 'lokasiTujuan'])
            ->where('jenis_transaksi', 'Mutasi')
            ->latest()
            ->paginate(10);

        return view('admin.mutasi.index', compact('locations', 'selectedItem', 'riwayat'));
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
            'status' => 'selesai',
            'keterangan' => $validated['keterangan'] ?? null,
            'lokasi_asal_id' => $item->location_id,
            'lokasi_tujuan_id' => $validated['lokasi_tujuan_id'],
        ]);

        $item->update(['location_id' => $validated['lokasi_tujuan_id']]);

        return redirect()
            ->route('admin.transaksi.mutasi.index')
            ->with('success', 'Mutasi lokasi berhasil dicatat.');
    }
}