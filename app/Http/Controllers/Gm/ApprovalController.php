<?php

namespace App\Http\Controllers\Gm;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index()
    {
        $stockOutPending = Transaction::with(['item', 'user'])
            ->where('jenis_transaksi', 'stock_out')
            ->where('status', 'diproses')
            ->latest()
            ->get();

        $mutasiPending = Transaction::with(['item', 'user', 'lokasiAsal', 'lokasiTujuan'])
            ->where('jenis_transaksi', 'Mutasi')
            ->whereNull('gm_approved_at')
            ->latest()
            ->get();

        return view('gm.approval.index', compact('stockOutPending', 'mutasiPending'));
    }

    // Stock Out: approval GM = final, langsung ubah status barang
    public function approveStockOut(Transaction $transaction)
    {
        if ($transaction->status !== 'diproses') {
            return back()->with('error', 'Transaksi ini belum diproses admin atau sudah final.');
        }

        $transaction->update([
            'status' => 'disetujui',
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
        ]);

        $transaction->item->update(['status' => 'dipinjam']);

        return back()->with('success', 'Peminjaman disetujui, barang resmi dipinjam.');
    }

    public function rejectStockOut(Transaction $transaction)
    {
        if ($transaction->status !== 'diproses') {
            return back()->with('error', 'Transaksi ini belum diproses admin atau sudah final.');
        }

        $transaction->update([
            'status' => 'ditolak',
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan ditolak oleh GM.');
    }

    // Mutasi: cuma pencatatan/pengesahan, tidak mengubah apapun di items
    public function acknowledgeMutasi(Transaction $transaction)
    {
        $transaction->update([
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
        ]);

        return back()->with('success', 'Mutasi telah disahkan oleh GM.');
    }
}