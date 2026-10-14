<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\PenyusutanService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanPenyusutanController extends Controller
{
    public function index(Request $request, PenyusutanService $service)
    {
        $data = $request->validate([
            'mode'  => ['nullable', 'in:bulanan,tahunan'],
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'integer', 'between:1990,2100'],
        ]);

        $mode  = $data['mode']  ?? 'bulanan';
        $bulan = (int) ($data['bulan'] ?? now()->month);
        $tahun = (int) ($data['tahun'] ?? now()->year);

        if ($mode === 'tahunan') {
            $awal  = Carbon::create($tahun, 1, 1)->startOfDay();
            $akhir = Carbon::create($tahun, 12, 31)->endOfDay();
        } else {
            $awal  = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $akhir = $awal->copy()->endOfMonth();
        }

        $hariIni = now()->startOfDay();

        if ($awal->gt($hariIni)) {
            $status = 'belum';      // periode belum dimulai
        } elseif ($akhir->gte($hariIni)) {
            $status = 'berjalan';   // hari ini masih di dalam periode
        } else {
            $status = 'final';      // periode sudah lewat
        }

        $items = Item::with('category.golongan')
            ->whereNotNull('tanggal_terima')
            ->whereDate('tanggal_terima', '<=', $akhir)
            ->orderBy('tanggal_terima')
            ->get();

        $rows = $items->map(fn ($item) => $service->baris($item, $awal, $akhir));

        $totals = [
            'nilai_perolehan'    => $items->sum('nilai_perolehan'),
            'penyusutan_periode' => $rows->sum('penyusutan_periode'),
            'akumulasi'          => $rows->sum('akumulasi'),
            'nilai_buku'         => $rows->sum('nilai_buku'),
        ];

        return view('admin.laporan.penyusutan', compact(
            'rows', 'totals', 'mode', 'bulan', 'tahun', 'awal', 'akhir', 'status', 'hariIni'
        ));
    }
}