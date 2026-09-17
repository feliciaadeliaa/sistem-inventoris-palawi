<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PeminjamanAktifExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Transaction::with(['item.category', 'item.location', 'user'])
            ->where('jenis_transaksi', 'Stock Out')
            ->where('status', 'disetujui')
            ->when(!empty($this->filters['location_id']), function ($q) {
                $q->whereHas('item', fn ($sub) => $sub->where('location_id', $this->filters['location_id']));
            })
            ->when(!empty($this->filters['category_id']), function ($q) {
                $q->whereHas('item', fn ($sub) => $sub->where('category_id', $this->filters['category_id']));
            })
            ->when(!empty($this->filters['status_pinjam']), function ($q) {
                if ($this->filters['status_pinjam'] === 'terlambat') {
                    $q->whereDate('tanggal_kembali_estimasi', '<', now()->toDateString());
                } elseif ($this->filters['status_pinjam'] === 'aktif') {
                    $q->whereDate('tanggal_kembali_estimasi', '>=', now()->toDateString());
                }
            })
            ->latest('approved_at')
            ->get();
    }

    public function headings(): array
    {
        return ['Barang', 'Kode Item', 'Kategori', 'Lokasi', 'Dipinjam Oleh', 'Tgl Disetujui', 'Estimasi Kembali', 'Status'];
    }

    public function map($trx): array
    {
        $terlambat = $trx->tanggal_kembali_estimasi && $trx->tanggal_kembali_estimasi->isPast();

        return [
            $trx->item->nama_barang ?? '-',
            $trx->item->item_id ?? '-',
            $trx->item->category->nama_kategori ?? '-',
            $trx->item->location->nama_lokasi ?? '-',
            $trx->user->name ?? '-',
            $trx->approved_at?->format('d/m/Y') ?? '-',
            $trx->tanggal_kembali_estimasi?->format('d/m/Y') ?? '-',
            $terlambat ? 'Terlambat' : 'Aktif',
        ];
    }
}