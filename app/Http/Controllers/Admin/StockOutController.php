<?php
// app/Http/Controllers/Admin/StockOutController.php
namespace App\Http\Controllers\Admin;

use App\Exports\StockOutExport;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['item', 'user'])->where('jenis_transaksi', 'Stock Out');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'desc');
        $transactions = $query->orderBy('created_at', $sort)->paginate(15)->withQueryString();

        return view('admin.stock-out.index', compact('transactions'));
    }

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

    public function export(Request $request)
    {
        $ids = array_filter(explode(',', $request->get('selected_ids', '')));
        $filters = $request->only(['date_from', 'date_to', 'sort']);

        return Excel::download(
            new StockOutExport($filters, $ids),
            'stock-out-' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}