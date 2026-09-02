<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepairRequestController extends Controller
{
    /**
     * Daftar semua permintaan perbaikan untuk admin.
     */
    public function index()
    {
        $transactions = Transaction::with(['item', 'user'])
            ->jenis('permintaan_perbaikan')
            ->latest()
            ->paginate(15);

        return view('admin.repair.index', compact('transactions'));
    }

    /**
     * Admin klik "Diproses" -> diteruskan ke GM
     */
    public function process(Request $request, Transaction $transaction)
    {
        abort_unless(
            $transaction->jenis_transaksi === 'permintaan_perbaikan'
                && $transaction->status === 'menunggu_approval',
            422,
            'Transaksi tidak dalam status yang bisa diproses.'
        );

        $transaction->update([
            'status' => 'diproses',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Permintaan diteruskan ke GM.');
    }

    /**
     * Tutup transaksi dengan hasil akhir: Berhasil / Gagal
     */
    public function complete(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'hasil' => 'required|in:berhasil,gagal',
        ]);

        abort_unless(
            $transaction->jenis_transaksi === 'permintaan_perbaikan'
                && $transaction->status === 'dalam_perbaikan',
            422,
            'Transaksi belum dalam status Dalam Perbaikan.'
        );

        DB::transaction(function () use ($transaction, $validated) {
            $transaction->update([
                'status' => $validated['hasil'] === 'berhasil' ? 'selesai_berhasil' : 'selesai_gagal',
            ]);

            if ($validated['hasil'] === 'berhasil') {
                $transaction->item()->update(['status' => 'baik']);
            }
        });

        return redirect()->back()->with('success', 'Transaksi perbaikan ditutup.');
    }
}