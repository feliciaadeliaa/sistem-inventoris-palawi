<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BarangLookupController extends Controller
{
    /**
     * Menampilkan halaman Scan/Cari Barang.
     */
    public function index()
    {
        return view('barang.scan');
    }

    /**
     * AJAX: cari barang berdasarkan nama atau kode (item_id).
     * GET /barang/cari?q=...
     */
public function search(Request $request)
{
    $query = trim($request->get('q', ''));

    $hasFilter = $request->filled('category_id')
        || $request->filled('location_id')
        || $request->filled('kondisi')
        || $request->filled('status')
        || $request->filled('golongan_at')
        || $request->filled('tahun_dari')
        || $request->filled('tahun_sampai');

    // Kalau tidak ada keyword sama sekali dan tidak ada filter, jangan query berat.
    if ($query === '' && ! $hasFilter) {
        return response()->json([]);
    }

    $items = Item::with(['category', 'location'])
        ->where('is_active', true)
        ->when($query !== '', function ($q) use ($query) {
            $q->where(function ($sub) use ($query) {
                $sub->where('nama_barang', 'like', "%{$query}%")
                    ->orWhere('item_id', 'like', "%{$query}%");
            });
        })
        ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
        ->when($request->filled('location_id'), fn ($q) => $q->where('location_id', $request->location_id))
        ->when($request->filled('kondisi'), fn ($q) => $q->where('kondisi', $request->kondisi))
        ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
        ->when($request->filled('golongan_at'), fn ($q) => $q->where('golongan_at', $request->golongan_at))
        ->when($request->filled('tahun_dari'), fn ($q) => $q->where('tahun_perolehan', '>=', $request->tahun_dari))
        ->when($request->filled('tahun_sampai'), fn ($q) => $q->where('tahun_perolehan', '<=', $request->tahun_sampai))
        ->limit(20)
        ->get();

    $isAdmin = $request->user()->role === 'admin';

    return response()->json(
        $items->map(fn (Item $item) => $this->formatItem($item, $isAdmin))
    );
}

    /**
     * AJAX: detail barang berdasarkan item_id (hasil scan QR).
     * GET /barang/{item_id}/detail
     */
    public function show(Request $request, string $itemId)
    {
        $item = Item::with(['category', 'location'])
            ->where('item_id', $itemId)
            ->where('is_active', true)
            ->first();

        if (! $item) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        $isAdmin = $request->user()->role === 'admin';

        return response()->json($this->formatItem($item, $isAdmin));
    }

    private function formatItem(Item $item, bool $withHistory = false): array
    {
        $sedangDiajukan = Transaction::where('item_id', $item->id)
            ->where('jenis_transaksi', 'Stock Out')
            ->whereIn('status', ['menunggu_approval', 'diproses'])
            ->exists();

        $data = [
            'id'              => $item->id,
            'item_id'         => $item->item_id,
            'nama_barang'     => $item->nama_barang,
            'kategori'        => $item->category->nama_kategori ?? '-',
            'lokasi'          => $item->location->nama_lokasi ?? '-',
            'kondisi'         => $item->kondisi,
            'status'          => $item->status,
            'sedang_diajukan' => $sedangDiajukan,
        ];

        // Riwayat transaksi hanya disertakan untuk Admin
        if ($withHistory) {
            $data['transactions'] = $item->transactions()
                ->with('user:id,name')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($t) => [
                    'jenis_transaksi' => $t->jenis_transaksi,
                    'status'          => $t->status,
                    'user'            => $t->user->name ?? '-',
                    'tanggal'         => $t->created_at->format('d M Y'),
                    'keterangan'      => $t->keterangan,
                ]);
        }

        return $data;
    }
}