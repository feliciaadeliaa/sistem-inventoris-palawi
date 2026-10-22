<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PenyusutanExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Services\PenyusutanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenyusutanController extends Controller
{
    private const NAMA_BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request, PenyusutanService $service)
    {
        $data = $request->validate($this->rules());

        return view('admin.laporan.penyusutan', $this->susunLaporan($data, $service) + [
            'namaBulan'  => self::NAMA_BULAN,
            'categories' => Category::orderBy('jenis_aktiva_tetap')->get(),
            'lokasis'    => Location::orderBy('nama_wisata')->get(),
        ]);
    }

    /**
     * Download Excel. Kalau ada ids[] (baris yang dicentang), hanya itu yang diunduh.
     * Kalau tidak ada yang dicentang, semua baris pada filter saat ini diunduh.
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
            : sprintf('%04d-%02d-sd-%02d', $laporan['tahun'], $laporan['bulanDari'], $laporan['bulanSampai']);

        return Excel::download(
            new PenyusutanExport($laporan['rows'], $laporan['bulanList'], self::NAMA_BULAN),
            "laporan-penyusutan-{$suffix}.xlsx"
        );
    }

    private function rules(): array
    {
        return [
            'mode'         => ['nullable', 'in:bulanan,tahunan'],
            'bulan_dari'   => ['nullable', 'integer', 'between:1,12'],
            'bulan_sampai' => ['nullable', 'integer', 'between:1,12', 'gte:bulan_dari'],
            'tahun'        => ['nullable', 'integer', 'between:1990,2100'],
            'category_id'  => ['nullable', 'string', 'max:5'],   // kode kategori, mis. C01
            'lokasi_id'    => ['nullable', 'integer'],            // locations.id
            'q'            => ['nullable', 'string', 'max:100'], // dikirim oleh search bar di header
        ];
    }

    /**
     * Logika laporan dipakai bersama oleh tampilan dan export supaya hasilnya selalu sama.
     */
    private function susunLaporan(array $data, PenyusutanService $service, array $ids = []): array
    {
        $mode       = $data['mode'] ?? 'bulanan';
        $tahun      = (int) ($data['tahun'] ?? now()->year);
        $search     = trim($data['q'] ?? '');
        $categoryId = $data['category_id'] ?? null;
        $lokasiId   = $data['lokasi_id'] ?? null;

        if ($mode === 'tahunan') {
            // Tahunan: selalu Januari - Desember pada tahun yang dipilih
            $bulanDari   = 1;
            $bulanSampai = 12;
        } else {
            $bulanDari   = (int) ($data['bulan_dari'] ?? now()->month);
            $bulanSampai = (int) ($data['bulan_sampai'] ?? $bulanDari);
        }

        $bulanList = range($bulanDari, $bulanSampai);

        $awal  = Carbon::create($tahun, $bulanDari, 1)->startOfDay();
        $akhir = Carbon::create($tahun, $bulanSampai, 1)->endOfMonth();

        $hariIni = now()->startOfDay();

        if ($awal->gt($hariIni)) {
            $status = 'belum';      // periode belum dimulai
        } elseif ($akhir->gte($hariIni)) {
            $status = 'berjalan';   // hari ini masih di dalam periode
        } else {
            $status = 'final';      // periode sudah lewat
        }

        $items = Item::with(['category.golongan', 'location'])
            ->whereNotNull('tanggal_terima')
            ->whereDate('tanggal_terima', '<=', $akhir)
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($lokasiId, fn ($query) => $query->where('location_id', $lokasiId))
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
                              $c->where('jenis_aktiva_tetap', 'like', "%{$search}%"))
                          ->orWhereHas('location', fn ($l) =>
                              $l->where('nama_wisata', 'like', "%{$search}%"));
                    });
                })
            )
            ->orderBy('tanggal_terima')
            ->get();

        $rows = $items->map(fn ($item) => $service->baris($item, $awal, $akhir, $bulanList));

        return compact(
            'rows', 'mode', 'tahun', 'bulanDari', 'bulanSampai', 'bulanList',
            'awal', 'akhir', 'status', 'hariIni', 'search', 'categoryId', 'lokasiId'
        );
    }
}