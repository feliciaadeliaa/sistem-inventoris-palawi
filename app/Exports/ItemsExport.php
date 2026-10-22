<?php

namespace App\Exports;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        protected array $ids = [],
        protected array $filters = []
    ) {}

    public function query(): Builder
    {
        $query = Item::query()
            ->with(['category', 'location'])
            ->orderBy('nomor_urut');

        // Kalau ada checkbox yang dipilih: export hanya itu (filter diabaikan,
        // karena pilihan bisa berasal dari halaman/filter yang berbeda).
        if (!empty($this->ids)) {
            return $query->whereIn('id', $this->ids);
        }

        // Kalau tidak ada yang dipilih: ikuti filter yang sedang aktif di halaman index.
        $f = $this->filters;

        return $query
            ->when(!empty($f['q']), function ($q) use ($f) {
                $search = $f['q'];
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('item_id', 'like', "%{$search}%")
                        ->orWhere('nomor_aktiva_tetap', 'like', "%{$search}%");
                });
            })
            ->when(!empty($f['category_id']), fn ($q) => $q->where('category_id', $f['category_id']))
            ->when(!empty($f['location_id']), fn ($q) => $q->where('location_id', $f['location_id']))
            ->when(!empty($f['kondisi']), fn ($q) => $q->where('kondisi', $f['kondisi']))
            ->when(!empty($f['status']), fn ($q) => $q->where('status', $f['status']))
            ->when(!empty($f['at_ibat']), fn ($q) => $q->where('at_ibat', $f['at_ibat']))
            ->when(!empty($f['tahun_dari']), fn ($q) => $q->where('tahun_perolehan', '>=', $f['tahun_dari']))
            ->when(!empty($f['tahun_sampai']), fn ($q) => $q->where('tahun_perolehan', '<=', $f['tahun_sampai']));
    }

    public function headings(): array
    {
        return [
            'Item ID',
            'Nama Barang',
            'Kategori',
            'Lokasi',
            'AT/IBAT',
            'Nomor Aktiva Tetap',
            'Tahun Perolehan',
            'Masa Manfaat (tahun)',
            'Nilai Perolehan',
            'Tanggal Terima',
            'Kondisi',
            'Status',
            // Kolom foto sengaja tidak ikut
        ];
    }

    public function map($item): array
    {
        return [
            $item->item_id,
            $item->nama_barang,
            $item->category->nama_kategori ?? '-',
            $item->location->nama_lokasi ?? '-',
            $item->at_ibat,
            $item->nomor_aktiva_tetap ?? '-',
            $item->tahun_perolehan,
            $item->masa_manfaat,
            $item->nilai_perolehan,
            $item->tanggal_terima?->format('d/m/Y'),
            $item->kondisi,
            str_replace('_', ' ', $item->status),
        ];
    }
}