<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RiwayatBarangExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;
    protected array $statusLabels;

    public function __construct(array $filters, array $statusLabels)
    {
        $this->filters = $filters;
        $this->statusLabels = $statusLabels;
    }

    public function collection()
    {
        return Transaction::with(['item.category', 'item.location', 'user'])
            ->when(!empty($this->filters['tanggal_dari']), fn ($q) => $q->whereDate('created_at', '>=', $this->filters['tanggal_dari']))
            ->when(!empty($this->filters['tanggal_sampai']), fn ($q) => $q->whereDate('created_at', '<=', $this->filters['tanggal_sampai']))
            ->when(!empty($this->filters['location_id']), function ($q) {
                $q->whereHas('item', fn ($sub) => $sub->where('location_id', $this->filters['location_id']));
            })
            ->when(!empty($this->filters['category_id']), function ($q) {
                $q->whereHas('item', fn ($sub) => $sub->where('category_id', $this->filters['category_id']));
            })
            ->when(!empty($this->filters['jenis_transaksi']), fn ($q) => $q->where('jenis_transaksi', $this->filters['jenis_transaksi']))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Barang', 'Kode Item', 'Kategori', 'Lokasi', 'Jenis', 'Status', 'Pemohon', 'Keterangan'];
    }

    public function map($trx): array
    {
        return [
            $trx->created_at->format('d/m/Y H:i'),
            $trx->item->nama_barang ?? '-',
            $trx->item->item_id ?? '-',
            $trx->item->category->nama_kategori ?? '-',
            $trx->item->location->nama_lokasi ?? '-',
            $trx->jenis_transaksi,
            $this->statusLabels[$trx->status] ?? ucfirst($trx->status),
            $trx->user->name ?? '-',
            $trx->keterangan ?? '-',
        ];
    }
}