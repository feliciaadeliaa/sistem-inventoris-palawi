<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StockInExport;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['item', 'user'])
            ->where('jenis_transaksi', 'Stock Out')
            ->whereIn('status', ['disetujui', 'dikembalikan']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'desc');
        $transactions = $query->orderBy('created_at', $sort)->paginate(15)->withQueryString();

        return view('admin.stock-in.index', compact('transactions'));
    }

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

    public function export(Request $request)
    {
        $ids = array_filter(explode(',', $request->get('selected_ids', '')));
        $filters = $request->only(['date_from', 'date_to', 'sort']);

        return Excel::download(
            new StockInExport($filters, $ids),
            'stock-in-' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}