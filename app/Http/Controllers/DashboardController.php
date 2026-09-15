<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        return match ($role) {
            'admin' => $this->adminDashboard(),
            'gm' => $this->gmDashboard(),
            default => $this->userDashboard(),
        };
    }

    private function adminDashboard()
    {
        $stats = [
            'total_barang' => Item::count(),
            'barang_tersedia' => Item::where('status', 'tersedia')->count(),
            'barang_dipinjam' => Item::where('status', 'dipinjam')->count(),
            'barang_nonaktif' => Item::where('status', 'nonaktif')->count(),
            'stock_out_menunggu' => Transaction::where('jenis_transaksi', 'Stock Out')
                ->whereIn('status', ['menunggu_approval', 'diproses'])->count(),
            'mutasi_menunggu' => Transaction::where('jenis_transaksi', 'Mutasi')
                ->where('status', 'menunggu_approval')->count(),
        ];

        $kondisiChart = Item::selectRaw('kondisi, COUNT(*) as total')
            ->groupBy('kondisi')
            ->pluck('total', 'kondisi');

        $bulanChart = $this->transaksiPerBulan();

        $recentActivities = Transaction::with(['item', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        $jatuhTempo = Transaction::with(['item', 'user'])
            ->where('jenis_transaksi', 'Stock Out')
            ->where('status', 'disetujui')
            ->whereNotNull('tanggal_kembali_estimasi')
            ->where('tanggal_kembali_estimasi', '<=', now()->addDays(3))
            ->orderBy('tanggal_kembali_estimasi')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'role' => 'admin',
            'stats' => $stats,
            'kondisiChart' => $kondisiChart,
            'bulanChart' => $bulanChart,
            'recentActivities' => $recentActivities,
            'jatuhTempo' => $jatuhTempo,
        ]);
    }

    private function gmDashboard()
    {
        $userId = Auth::id();

        $stats = [
            'stock_out_menunggu' => Transaction::where('jenis_transaksi', 'Stock Out')
                ->where('status', 'diproses')->count(),
            'mutasi_menunggu' => Transaction::where('jenis_transaksi', 'Mutasi')
                ->where('status', 'menunggu_approval')->count(),
            'total_disetujui' => Transaction::where('gm_approved_by', $userId)
                ->where('status', 'disetujui')->count(),
            'total_ditolak' => Transaction::where('gm_approved_by', $userId)
                ->where('status', 'ditolak')->count(),
        ];

        $bulanChart = $this->approvalPerBulan($userId);

        $recentActivities = Transaction::with(['item', 'user'])
            ->where('gm_approved_by', $userId)
            ->latest('gm_approved_at')
            ->limit(10)
            ->get();

        $pendingList = Transaction::with(['item', 'user'])
            ->where(function ($q) {
                $q->where(['jenis_transaksi' => 'Stock Out', 'status' => 'diproses'])
                    ->orWhere(['jenis_transaksi' => 'Mutasi', 'status' => 'menunggu_approval']);
            })
            ->oldest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'role' => 'gm',
            'stats' => $stats,
            'bulanChart' => $bulanChart,
            'recentActivities' => $recentActivities,
            'pendingList' => $pendingList,
        ]);
    }

    private function userDashboard()
    {
        $userId = Auth::id();

        $stats = [
            'menunggu' => Transaction::where('user_id', $userId)
                ->where('jenis_transaksi', 'Stock Out')
                ->whereIn('status', ['menunggu_approval', 'diproses'])->count(),
            'disetujui' => Transaction::where('user_id', $userId)
                ->where('jenis_transaksi', 'Stock Out')
                ->where('status', 'disetujui')->count(),
            'ditolak' => Transaction::where('user_id', $userId)
                ->where('jenis_transaksi', 'Stock Out')
                ->where('status', 'ditolak')->count(),
            'dikembalikan' => Transaction::where('user_id', $userId)
                ->where('jenis_transaksi', 'Stock Out')
                ->where('status', 'dikembalikan')->count(),
        ];

        $statusChart = Transaction::where('user_id', $userId)
            ->where('jenis_transaksi', 'Stock Out')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentActivities = Transaction::with(['item'])
            ->where('user_id', $userId)
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', [
            'role' => 'user',
            'stats' => $stats,
            'statusChart' => $statusChart,
            'recentActivities' => $recentActivities,
        ]);
    }

    private function transaksiPerBulan()
    {
        $bulanData = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $bulanData[$date->format('Y-m')] = ['Stock In' => 0, 'Stock Out' => 0, 'Mutasi' => 0];
        }

        $raw = Transaction::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, jenis_transaksi, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('bulan', 'jenis_transaksi')
            ->get();

        foreach ($raw as $row) {
            if (isset($bulanData[$row->bulan][$row->jenis_transaksi])) {
                $bulanData[$row->bulan][$row->jenis_transaksi] = $row->total;
            }
        }

        return [
            'labels' => $labels,
            'stock_in' => array_column($bulanData, 'Stock In'),
            'stock_out' => array_column($bulanData, 'Stock Out'),
            'mutasi' => array_column($bulanData, 'Mutasi'),
        ];
    }

    private function approvalPerBulan($userId)
    {
        $labels = [];
        $approvedData = [];
        $rejectedData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $approvedData[] = Transaction::where('gm_approved_by', $userId)
                ->where('status', 'disetujui')
                ->whereYear('gm_approved_at', $date->year)
                ->whereMonth('gm_approved_at', $date->month)
                ->count();

            $rejectedData[] = Transaction::where('gm_approved_by', $userId)
                ->where('status', 'ditolak')
                ->whereYear('gm_approved_at', $date->year)
                ->whereMonth('gm_approved_at', $date->month)
                ->count();
        }

        return ['labels' => $labels, 'approved' => $approvedData, 'rejected' => $rejectedData];
    }
}