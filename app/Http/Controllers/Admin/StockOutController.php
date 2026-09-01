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
            ->where('jenis_transaksi', 'stock_out')
            ->latest()
            ->paginate(15);

        return view('admin.stock-out.index', compact('transactions'));
    }

    // Admin proses tahap 1 - diteruskan ke GM, TIDAK langsung ubah status barang
    public function approve(Transaction $transaction)
    {
        if ($transaction->status !== 'menunggu_approval') {
            return back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        $transaction->update([
            'status' => 'diproses',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan diproses, diteruskan ke GM untuk persetujuan akhir.');
    }

    // Tolak pengajuan (admin masih bisa tolak di tahap awal)
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
}