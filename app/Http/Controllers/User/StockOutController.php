<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\PeminjamanSayaExport;
use Maatwebsite\Excel\Facades\Excel;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['item', 'penolak'])
            ->where('jenis_transaksi', 'Stock Out')
            ->where('user_id', Auth::id());

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'desc') === 'asc' ? 'asc' : 'desc';

        $transactions = $query->orderBy('created_at', $sort)
            ->paginate(15)
            ->withQueryString();

        return view('users.stock-out.index', compact('transactions'));
    }

    public function create(?string $item_id = null)
    {
        $item = null;
        $catatan = null;

        if ($item_id) {
            $item = Item::where('item_id', $item_id)->first();

            if (!$item) {
                return redirect()->route('peminjaman.create')
                    ->with('error', 'Barang tidak ditemukan.');
            }

            $catatan = $this->catatanKetersediaan($item);
        }

        return view('users.stock-out.create', compact('item', 'catatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,item_id',
            'keterangan' => 'nullable|string|max:1000',
            'tanggal_kembali_estimasi' => 'required|date|after_or_equal:today',
        ]);

        $item = Item::where('item_id', $validated['item_id'])->firstOrFail();

        $catatan = $this->catatanKetersediaan($item);
        if ($catatan) {
            return back()->withInput()->with('error', $catatan);
        }

        Transaction::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'jenis_transaksi' => 'Stock Out',
            'status' => 'menunggu_approval',
            'keterangan' => $validated['keterangan'] ?? null,
            'tanggal_kembali_estimasi' => $validated['tanggal_kembali_estimasi'],
        ]);

        return redirect()->route('peminjaman.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim, menunggu approval admin.');
    }

    /**
     * Mengembalikan teks catatan kalau barang belum bisa diajukan, atau null kalau boleh.
     */
    private function catatanKetersediaan(Item $item): ?string
    {
        if ($item->status === 'dipinjam') {
            $estimasi = Transaction::where('item_id', $item->id)
                ->where('jenis_transaksi', 'Stock Out')
                ->where('status', 'disetujui')
                ->latest('id')
                ->value('tanggal_kembali_estimasi');

            $teks = 'Barang ini sedang dipinjam';
            if ($estimasi) {
                $teks .= ' dan diperkirakan kembali pada ' . Carbon::parse($estimasi)->translatedFormat('d F Y');
            }

            return $teks . '. Silakan ajukan lagi setelah barang dikembalikan.';
        }

        if ($item->status !== 'tersedia') {
            return 'Barang ini sedang tidak tersedia untuk dipinjam.';
        }

        $sedangDiajukan = Transaction::where('item_id', $item->id)
            ->where('jenis_transaksi', 'Stock Out')
            ->whereIn('status', ['menunggu_approval', 'diproses'])
            ->exists();

        if ($sedangDiajukan) {
            return 'Barang ini sudah diajukan pengguna lain dan sedang menunggu proses persetujuan.';
        }

        return null;
    }

    public function export(Request $request)
    {
        $ids = array_filter(explode(',', $request->get('selected_ids', '')));
        $filters = $request->only(['date_from', 'date_to', 'sort']);

        return Excel::download(
            new PeminjamanSayaExport($filters, $ids),
            'riwayat-peminjaman-' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}