<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RiwayatBarangExport;

class LaporanController extends Controller
{
    public const STATUS_LABELS = [
        'menunggu_approval' => 'Menunggu',
        'diproses' => 'Diproses',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'dikembalikan' => 'Dikembalikan',
        'selesai' => 'Selesai',
    ];

    // ==== Tab: Riwayat per Barang (log transaksi gabungan, bisa difilter) ====

    public function riwayatBarangIndex(Request $request)
    {
        $transactions = $this->filteredTransactionsQuery($request)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.laporan.riwayat-barang-index', [
            'transactions' => $transactions,
            'locations' => Location::orderBy('nama_lokasi')->get(),
            'categories' => Category::orderBy('nama_kategori')->get(),
            'jenisOptions' => Transaction::distinct()->orderBy('jenis_transaksi')->pluck('jenis_transaksi'),
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }

    public function exportRiwayatBarangPdf(Request $request)
    {
        $transactions = $this->filteredTransactionsQuery($request)->latest()->get();

        $pdf = Pdf::loadView('admin.laporan.riwayat-barang-pdf', [
            'transactions' => $transactions,
            'statusLabels' => self::STATUS_LABELS,
            'filterInfo' => $this->filterSummary($request),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('riwayat-barang.pdf');
    }

    public function exportRiwayatBarangExcel(Request $request)
    {
        return Excel::download(
            new RiwayatBarangExport($request->all(), self::STATUS_LABELS),
            'riwayat-barang.xlsx'
        );
    }

        // ==== Tab: Peminjaman Aktif/Terlambat ====

    public function peminjamanAktifIndex(Request $request)
    {
        $data = $this->filteredPeminjamanQuery($request)
            ->latest('approved_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.laporan.peminjaman-aktif-index', [
            'transactions' => $data,
            'locations' => Location::orderBy('nama_lokasi')->get(),
            'categories' => Category::orderBy('nama_kategori')->get(),
        ]);
    }

    public function exportPeminjamanAktifPdf(Request $request)
    {
        $transactions = $this->filteredPeminjamanQuery($request)->latest('approved_at')->get();

        $pdf = Pdf::loadView('admin.laporan.peminjaman-aktif-pdf', [
            'transactions' => $transactions,
            'filterInfo' => $this->filterSummary($request),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('peminjaman-aktif.pdf');
    }

    public function exportPeminjamanAktifExcel(Request $request)
    {
        return Excel::download(
            new \App\Exports\PeminjamanAktifExport($request->all()),
            'peminjaman-aktif.xlsx'
        );
    }

    private function filteredPeminjamanQuery(Request $request): Builder
    {
        return Transaction::with(['item.category', 'item.location', 'user'])
            ->where('jenis_transaksi', 'Stock Out')
            ->where('status', 'disetujui')
            ->when($request->filled('location_id'), function ($q) use ($request) {
                $q->whereHas('item', fn ($sub) => $sub->where('location_id', $request->location_id));
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->whereHas('item', fn ($sub) => $sub->where('category_id', $request->category_id));
            })
            ->when($request->filled('status_pinjam'), function ($q) use ($request) {
                if ($request->status_pinjam === 'terlambat') {
                    $q->whereDate('tanggal_kembali_estimasi', '<', now()->toDateString());
                } elseif ($request->status_pinjam === 'aktif') {
                    $q->whereDate('tanggal_kembali_estimasi', '>=', now()->toDateString());
                }
            });
    }

    public function riwayatBarangShow(Item $item)
    {
        $item->load(['category', 'location']);

        $transactions = $item->transactions()
            ->with(['user', 'approver', 'gmApprover', 'lokasiAsal', 'lokasiTujuan'])
            ->orderBy('created_at')
            ->get();

        $timeline = collect();

        $timeline->push([
            'tanggal' => $item->created_at,
            'judul' => 'Barang didaftarkan ke sistem',
            'oleh' => null,
            'keterangan' => 'Master data barang dibuat',
        ]);

        foreach ($transactions as $trx) {
            if ($trx->jenis_transaksi === 'Stock Out') {
                $timeline->push([
                    'tanggal' => $trx->created_at,
                    'judul' => 'Pengajuan Peminjaman (Stock Out)',
                    'oleh' => $trx->user->name ?? '-',
                    'keterangan' => $trx->keterangan,
                ]);

                if ($trx->approved_at) {
                    $timeline->push([
                        'tanggal' => $trx->approved_at,
                        'judul' => 'Diproses Admin',
                        'oleh' => $trx->approver->name ?? '-',
                        'keterangan' => null,
                    ]);
                }

                if ($trx->gm_approved_at) {
                    $label = $trx->status === 'ditolak' ? 'Ditolak GM' : 'Disetujui GM';
                    $timeline->push([
                        'tanggal' => $trx->gm_approved_at,
                        'judul' => $label,
                        'oleh' => $trx->gmApprover->name ?? '-',
                        'keterangan' => null,
                    ]);
                }

                if ($trx->tanggal_kembali_aktual) {
                    $timeline->push([
                        'tanggal' => $trx->tanggal_kembali_aktual,
                        'judul' => 'Barang Dikembalikan (Stock In)',
                        'oleh' => $trx->user->name ?? '-',
                        'keterangan' => null,
                    ]);
                }
            }

            if ($trx->jenis_transaksi === 'Mutasi') {
                $timeline->push([
                    'tanggal' => $trx->created_at,
                    'judul' => 'Mutasi Lokasi',
                    'oleh' => $trx->user->name ?? '-',
                    'keterangan' => ($trx->lokasiAsal->nama_lokasi ?? '-').' → '.($trx->lokasiTujuan->nama_lokasi ?? '-'),
                ]);

                if ($trx->gm_approved_at) {
                    $timeline->push([
                        'tanggal' => $trx->gm_approved_at,
                        'judul' => 'Mutasi Disahkan GM',
                        'oleh' => $trx->gmApprover->name ?? '-',
                        'keterangan' => null,
                    ]);
                }
            }
        }

        $timeline = $timeline->sortBy('tanggal')->values();

        return view('admin.laporan.riwayat-barang-show', compact('item', 'timeline'));
    }

    // ==== Helper: query filter dipakai bersama web, PDF, Excel ====

    private function filteredTransactionsQuery(Request $request): Builder
    {
        return Transaction::with(['item.category', 'item.location', 'user'])
            ->when($request->filled('tanggal_dari'), fn ($q) => $q->whereDate('created_at', '>=', $request->tanggal_dari))
            ->when($request->filled('tanggal_sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->tanggal_sampai))
            ->when($request->filled('location_id'), function ($q) use ($request) {
                $q->whereHas('item', fn ($sub) => $sub->where('location_id', $request->location_id));
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->whereHas('item', fn ($sub) => $sub->where('category_id', $request->category_id));
            })
            ->when($request->filled('jenis_transaksi'), fn ($q) => $q->where('jenis_transaksi', $request->jenis_transaksi));
    }

    private function filterSummary(Request $request): string
    {
    $parts = [];
    if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
        $parts[] = 'Periode: '.($request->tanggal_dari ?? '...').' s/d '.($request->tanggal_sampai ?? '...');
    }
    if ($request->filled('jenis_transaksi')) {
        $parts[] = 'Jenis: '.$request->jenis_transaksi;
    }
    if ($request->filled('location_id')) {
        $parts[] = 'Lokasi: '.(Location::find($request->location_id)->nama_lokasi ?? '-');
    }
    if ($request->filled('category_id')) {
        $parts[] = 'Kategori: '.(Category::where('category_id', $request->category_id)->value('nama_kategori') ?? '-');
    }
    if ($request->filled('status_pinjam')) {
        $parts[] = 'Status: '.ucfirst($request->status_pinjam);
    }

    return $parts ? implode(' | ', $parts) : 'Semua data';
}
}