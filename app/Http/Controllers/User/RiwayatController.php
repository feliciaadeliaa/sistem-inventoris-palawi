<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public const JENIS_LABELS = [
        'Stock Out'            => 'Peminjaman',
        'permintaan_perbaikan' => 'Permintaan Perbaikan',
        'laporan_kerusakan'    => 'Laporan Kerusakan',
    ];

    public const STATUS_LABELS = [
        'menunggu_approval' => 'Menunggu admin',
        'diproses'          => 'Menunggu persetujuan GM',
        'disetujui'         => 'Disetujui',
        'dalam_perbaikan'   => 'Dalam perbaikan',
        'selesai'           => 'Selesai',
        'dikembalikan'      => 'Dikembalikan',
        'ditolak'           => 'Ditolak',
    ];

    public function index(Request $request)
    {
        $query = Transaction::with(['item', 'penolak'])
            ->where('user_id', Auth::id())
            ->whereIn('jenis_transaksi', array_keys(self::JENIS_LABELS));

        if ($request->filled('jenis') && array_key_exists($request->jenis, self::JENIS_LABELS)) {
            $query->where('jenis_transaksi', $request->jenis);
        }
        if ($request->filled('status') && array_key_exists($request->status, self::STATUS_LABELS)) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'desc') === 'asc' ? 'asc' : 'desc';

        $transactions = $query->orderBy('created_at', $sort)
            ->paginate(15)
            ->withQueryString();

        return view('users.riwayat.index', [
            'transactions' => $transactions,
            'jenisLabels'  => self::JENIS_LABELS,
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }
}