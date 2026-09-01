<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class StockInController extends Controller
{
    // Daftar barang yang sedang dipinjam, menunggu dikembalikan
    public function index()
    {
        $transactions = Transaction::with(['item', 'user'])
            ->where('jenis_transaksi', 'Stock Out')
            ->whereIn('status', ['disetujui', 'dikembalikan'])
            ->latest()
            ->paginate(15);

        return view('admin.stock-in.index', compact('transactions'));
    }

    // Konfirmasi pengembalian (scan ulang QR barang)
    public function confirmReturn(Transaction $transaction)
    {
        if ($transaction->status !== 'disetujui') {
            return back()->with('error', 'Transaksi ini bukan barang yang sedang dipinjam.');
        }

        $transaction->update([
            'status' => 'dikembalikan',
            'tanggal_kembali_aktual' => now(),
        ]);

        $transaction->item->update(['status' => 'tersedia']);

        return back()->with('success', 'Barang berhasil dikonfirmasi kembali, status jadi Tersedia.');
    }
}