<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOutController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['item'])
            ->where('jenis_transaksi', 'Stock Out')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('users.stock-out.index', compact('transactions'));
    }

    public function create(?string $item_id = null)
    {
        $item = null;

        if ($item_id) {
            $item = Item::where('item_id', $item_id)->first();

            if (!$item) {
                return redirect()->route('peminjaman.create')
                    ->with('error', 'Barang tidak ditemukan.');
            }

            if ($item->status !== 'tersedia') {
                return back()->with('error', 'Barang ini sedang tidak tersedia untuk dipinjam.');
            }
        }

        return view('users.stock-out.create', compact('item'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,item_id',
            'keterangan' => 'required|string|max:1000',
            'tanggal_kembali_estimasi' => 'required|date|after_or_equal:today',
        ]);

        $item = Item::where('item_id', $validated['item_id'])->firstOrFail();

        if ($item->status !== 'tersedia') {
            return back()->with('error', 'Barang ini sedang tidak tersedia untuk dipinjam.');
        }

        Transaction::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'jenis_transaksi' => 'Stock Out',
            'status' => 'menunggu_approval',
            'keterangan' => $validated['keterangan'],
            'tanggal_kembali_estimasi' => $validated['tanggal_kembali_estimasi'],
        ]);

        return redirect()->route('peminjaman.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim, menunggu approval admin.');
    }
}