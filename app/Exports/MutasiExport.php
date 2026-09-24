<?php
// app/Exports/MutasiExport.php
namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MutasiExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected array $filters = [], protected array $ids = []) {}

    public function collection(): Collection
    {
        $query = Transaction::with(['item', 'lokasiAsal', 'lokasiTujuan'])
            ->where('jenis_transaksi', 'Mutasi');

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        } else {
            if (!empty($this->filters['date_from'])) {
                $query->whereDate('created_at', '>=', $this->filters['date_from']);
            }
            if (!empty($this->filters['date_to'])) {
                $query->whereDate('created_at', '<=', $this->filters['date_to']);
            }
        }

        return $query->orderBy('created_at', $this->filters['sort'] ?? 'desc')->get();
    }

    public function headings(): array
    {
        return ['Nama Barang', 'Lokasi Asal', 'Lokasi Tujuan', 'Status', 'Tanggal'];
    }

    public function map($trx): array
    {
        return [
            $trx->item->nama_barang,
            $trx->lokasiAsal->nama_lokasi ?? '-',
            $trx->lokasiTujuan->nama_lokasi ?? '-',
            str_replace('_', ' ', ucfirst($trx->status)),
            $trx->created_at->format('d M Y H:i'),
        ];
    }
}