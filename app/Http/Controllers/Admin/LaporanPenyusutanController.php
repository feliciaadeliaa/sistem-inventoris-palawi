<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PenyusutanExport;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\PenyusutanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenyusutanController extends Controller
{
    public function index(Request $request, PenyusutanService $service)
    {
        $data = $request->validate($this->rules());

        return view('admin.laporan.penyusutan', $this->susunLaporan($data, $service));
    }

    /**
     * Download Excel. Kalau ada ids[] (baris yang dicentang), hanya itu yang diunduh.
     * Kalau tidak ada yang dicentang, semua baris pada periode + pencarian saat ini diunduh.
     */
    public function export(Request $request, PenyusutanService $service)
    {
        $data = $request->validate($this->rules() + [
            'ids'   => ['nullable', 'array'],
            'ids.*' => ['integer'],
        ]);

        $laporan = $this->susunLaporan($data, $service, $data['ids'] ?? []);

        $suffix = $laporan['mode'] === 'tahunan'
            ? $laporan['tahun']
            : sprintf('%04d-%02d', $laporan['tahun'], $laporan['bulan']);

        return Excel::download(
            new PenyusutanExport($laporan['rows']),
            "laporan-penyusutan-{$suffix}.xlsx"
        );
    }

    private function rules(): array
    {
        return [
            'mode'  => ['nullable', 'in:bulanan,tahunan'],
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'integer', 'between:1990,2100'],
            'q'     => ['nullable', 'string', 'max:100'], // dikirim oleh search bar di header
        ];
    }

    /**
     * Logika laporan dipakai bersama oleh tampilan dan export supaya hasilnya selalu sama.
     */
    private function susunLaporan(array $data, PenyusutanService $service, array $ids = []): array
    {
        $mode   = $data['mode']  ?? 'bulanan';
        $bulan  = (int) ($data['bulan'] ?? now()->month);
        $tahun  = (int) ($data['tahun'] ?? now()->year);
        $search = trim($data['q'] ?? '');

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
            // Baris yang dicentang diunduh apa adanya; pencarian hanya berlaku kalau tidak ada yang dicentang
            ->when(
                ! empty($ids),
                fn ($query) => $query->whereIn('id', $ids),
                fn ($query) => $query->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('item_id', 'like', "%{$search}%")
                          ->orWhere('nama_barang', 'like', "%{$search}%")
                          ->orWhere('nomor_aktiva_tetap', 'like', "%{$search}%")
                          ->orWhereHas('category', fn ($c) =>
                              $c->where('jenis_aktiva_tetap', 'like', "%{$search}%"));
                    });
                })
            )
            ->orderBy('tanggal_terima')
            ->get();

        $rows = $items->map(fn ($item) => $service->baris($item, $awal, $akhir));

        return compact('rows', 'mode', 'bulan', 'tahun', 'awal', 'akhir', 'status', 'hariIni', 'search');
    }
}