<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOutController extends Controller
{
    // Antrian approval
    public function index()
    {
        $transactions = Transaction::with(['item', 'user'])
            ->where('jenis_transaksi', 'Stock Out')
            ->latest()
            ->paginate(15);

        return view('admin.stock-out.index', compact('transactions'));
    }

    // Setujui pengajuan
    public function approve(Transaction $transaction)
    {
        if ($transaction->status !== 'menunggu_approval') {
            return back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        $transaction->update([
            'status' => 'disetujui',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $transaction->item->update(['status' => 'dipinjam']);

        return back()->with('success', 'Pengajuan disetujui, status barang diperbarui jadi Dipinjam.');
    }

    // Tolak pengajuan
    public function reject(Transaction $transaction)
    {
        if ($transaction->status !== 'menunggu_approval') {
            return back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        $transaction->update([
            'status' => 'ditolak',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan ditolak.');
    }

    // Konfirmasi pengembalian (scan ulang QR barang)
    public function confirmReturn(string $item_id)
    {
        $transaction = Transaction::whereHas('item', fn ($q) => $q->where('item_id', $item_id))
            ->where('jenis_transaksi', 'Stock Out')
            ->where('status', 'disetujui')
            ->latest()
            ->firstOrFail();

        $transaction->update([
            'status' => 'dikembalikan',
            'tanggal_kembali_aktual' => now(),
        ]);

        $transaction->item->update(['status' => 'tersedia']);

        return back()->with('success', 'Barang berhasil dikonfirmasi kembali, status jadi Tersedia.');
    }
}