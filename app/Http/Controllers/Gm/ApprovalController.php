<?php

namespace App\Http\Controllers\Gm;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
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

        return view('gm.approval.index', compact('stockOutPending', 'mutasiPending', 'repairPending'));
    }

    /**
     * Helper reject: alasan wajib diisi untuk SEMUA jenis transaksi.
     */
    private function tolak(Request $request, Transaction $transaction, string $jenis, string $statusAwal, string $pesanSukses)
    {
        $validated = $request->validate([
            'alasan_penolakan' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        if ($transaction->jenis_transaksi !== $jenis || $transaction->status !== $statusAwal) {
            return back()->with('error', 'Transaksi ini belum diproses atau sudah final.');
        }

        $transaction->update([
            'status' => 'ditolak',
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
            'alasan_penolakan' => $validated['alasan_penolakan'],
            'ditolak_oleh' => Auth::id(),
        ]);

        return back()->with('success', $pesanSukses);
    }

    // ===================== STOCK OUT =====================
    // Approval GM = final, langsung ubah status barang
    public function approveStockOut(Transaction $transaction)
    {
        if ($transaction->status !== 'diproses') {
            return back()->with('error', 'Transaksi ini belum diproses admin atau sudah final.');
        }

        if ($transaction->item->status === 'dipinjam') {
            return back()->with('error', "Barang \"{$transaction->item->nama_barang}\" sudah berstatus DIPINJAM. Tolak pengajuan ini atau tunggu barang dikembalikan.");
        }

        $transaction->update([
            'status' => 'disetujui',
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
        ]);

        $transaction->item->update(['status' => 'dipinjam']);

        return back()->with('success', 'Peminjaman disetujui, barang resmi dipinjam.');
    }

    public function rejectStockOut(Request $request, Transaction $transaction)
    {
        return $this->tolak($request, $transaction, 'Stock Out', 'diproses', 'Pengajuan ditolak oleh GM.');
    }

    // ===================== MUTASI =====================
    // Approval GM = final, langsung ubah lokasi barang
    public function approveMutasi(Transaction $transaction)
    {
        if ($transaction->status !== 'menunggu_approval') {
            return back()->with('error', 'Mutasi ini sudah diproses sebelumnya.');
        }

        // Barang bisa saja dipinjam setelah mutasi diajukan
        if ($transaction->item->status === 'dipinjam') {
            return back()->with('error', "Mutasi tidak bisa disetujui: barang \"{$transaction->item->nama_barang}\" sedang berstatus DIPINJAM. Tolak mutasi ini, lalu ajukan ulang setelah barang dikembalikan.");
        }

        $transaction->update([
            'status' => 'disetujui',
            'gm_approved_by' => Auth::id(),
            'gm_approved_at' => now(),
        ]);

        $transaction->item->update(['location_id' => $transaction->lokasi_tujuan_id]);

        return back()->with('success', 'Mutasi disetujui, lokasi barang diperbarui.');
    }

    public function rejectMutasi(Request $request, Transaction $transaction)
    {
        return $this->tolak($request, $transaction, 'Mutasi', 'menunggu_approval', 'Mutasi ditolak oleh GM.');
    }

    // ===================== PERBAIKAN =====================
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

    public function rejectRepair(Request $request, Transaction $transaction)
    {
        return $this->tolak($request, $transaction, 'permintaan_perbaikan', 'diproses', 'Permintaan perbaikan ditolak oleh GM.');
    }
}