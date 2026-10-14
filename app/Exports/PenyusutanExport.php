<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export Laporan Penyusutan Aset.
 * Menerima baris hasil PenyusutanService::baris(), yaitu array:
 * ['item' => Item, 'penyusutan_periode' => ..., 'akumulasi' => ..., 'nilai_buku' => ...]
 */
class PenyusutanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStyles
{
    private int $no = 0;

    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Item',
            'Nama Barang',
            'Jenis Aktiva',
            'No. Aktiva Tetap',
            'Tgl Terima',
            'Masa Manfaat (th)',
            'Nilai Perolehan',
            'Penyusutan Periode',
            'Akumulasi',
            'Nilai Buku',
        ];
    }

    public function map($row): array
    {
        $item = $row['item'];

        return [
            ++$this->no,
            $item->item_id,
            $item->nama_barang,
            $item->category->jenis_aktiva_tetap ?? '-',
            $item->nomor_aktiva_tetap ?? '-',
            $item->tanggal_terima ? Date::dateTimeToExcel($item->tanggal_terima) : null,
            (int) $item->masa_manfaat,
            (float) $item->nilai_perolehan,
            (float) $row['penyusutan_periode'],
            (float) $row['akumulasi'],
            (float) $row['nilai_buku'],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => '#,##0',
            'I' => '#,##0',
            'J' => '#,##0',
            'K' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00617A'], // brand-700
            ],
        ]);

        $sheet->freezePane('A2');

        return [];
    }
}
