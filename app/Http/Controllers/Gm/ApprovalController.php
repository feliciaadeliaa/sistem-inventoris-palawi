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
            ->where('jenis_transaksi', 'Stock Out')
            ->where('status', 'diproses')
            ->latest()
            ->get();

        $mutasiPending = Transaction::with(['item', 'user', 'lokasiAsal', 'lokasiTujuan'])
            ->where('jenis_transaksi', 'Mutasi')
            ->where('status', 'menunggu_approval')
            ->latest()
            ->get();

         $repairPending = Transaction::with(['item', 'user'])
        ->where('jenis_transaksi', 'permintaan_perbaikan')
        ->where('status', 'diproses')
        ->latest()
        ->get();

        return view('gm.approval.index', compact('stockOutPending', 'mutasiPending','repairPending'));
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

    // Mutasi: approval GM = final, langsung ubah lokasi barang
    public function approveMutasi(Transaction $transaction)
    {
        if ($transaction->status !== 'menunggu_approval') {
            return back()->with('error', 'Mutasi ini sudah diproses sebelumnya.');
        }

        $transaction->update([
            'status' => 'disetujui',
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
        ]);

        $transaction->item->update(['location_id' => $transaction->lokasi_tujuan_id]);

        return back()->with('success', 'Mutasi disetujui, lokasi barang diperbarui.');
    }

    public function rejectMutasi(Transaction $transaction)
    {
        if ($transaction->status !== 'menunggu_approval') {
            return back()->with('error', 'Mutasi ini sudah diproses sebelumnya.');
        }

        $transaction->update([
            'status' => 'ditolak',
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
        ]);

        return back()->with('success', 'Mutasi ditolak oleh GM.');
    }

 public function approveRepair(Transaction $transaction)
    {
        if ($transaction->jenis_transaksi !== 'permintaan_perbaikan' || $transaction->status !== 'diproses') {
            return back()->with('error', 'Transaksi ini belum diproses admin atau sudah final.');
        }

        $transaction->update([
            'status' => 'dalam_perbaikan',
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
        ]);

        $transaction->item->update(['status' => 'dalam_perbaikan']);

        return back()->with('success', 'Perbaikan disetujui GM, barang masuk status Dalam Perbaikan.');
    }

    public function rejectRepair(Transaction $transaction)
    {
        if ($transaction->jenis_transaksi !== 'permintaan_perbaikan' || $transaction->status !== 'diproses') {
            return back()->with('error', 'Transaksi ini belum diproses admin atau sudah final.');
        }

        $transaction->update([
            'status' => 'ditolak',
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
        ]);

        return back()->with('success', 'Permintaan perbaikan ditolak oleh GM.');
    }
}