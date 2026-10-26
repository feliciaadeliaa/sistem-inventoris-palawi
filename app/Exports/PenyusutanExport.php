<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenyusutanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStyles,
    WithStrictNullComparison
{
    private const KOLOM_TETAP = 9; // A..I sebelum kolom per bulan

    private int $no = 0;

    /**
     * @param Collection $rows       hasil PenyusutanService::baris()
     * @param int[]      $bulanList  angka bulan yang ditampilkan, mis. [1,2,3] atau 1..12
     * @param array      $namaBulan  [1 => 'Januari', ...]
     */
    public function __construct(
        private Collection $rows,
        private array $bulanList = [],
        private array $namaBulan = [],
    ) {
    }

    public function collection(): Collection
    {
        return $this->rows->values();
    }

    public function headings(): array
    {
        $head = [
            'No',
            'ID Item',
            'Nama Barang',
            'Jenis Aktiva',
            'Lokasi',
            'No. Aktiva Tetap',
            'Tgl Terima',
            'Masa Manfaat (th)',
            'Nilai Perolehan',
        ];

        foreach ($this->bulanList as $b) {
            $head[] = $this->namaBulan[$b] ?? (string) $b;
        }

        return array_merge($head, [
            'Total Penyusutan Periode',
            'Akumulasi',
            'Nilai Buku',
        ]);
    }

    public function map($row): array
    {
        $item = $row['item'];

        $line = [
            ++$this->no,
            $item->item_id,
            $item->nama_barang,
            $item->category->jenis_aktiva_tetap ?? '-',
            $item->location->nama_wisata ?? '-',
            $item->nomor_aktiva_tetap ?? '-',
            $item->tanggal_terima ? Date::dateTimeToExcel($item->tanggal_terima) : null,
            (int) $item->masa_manfaat,
            (float) $item->nilai_perolehan,
        ];

        foreach ($this->bulanList as $b) {
            $line[] = (float) ($row['per_bulan'][$b] ?? 0);
        }

        return array_merge($line, [
            (float) ($row['penyusutan_periode'] ?? 0),
            (float) ($row['akumulasi'] ?? 0),
            (float) ($row['nilai_buku'] ?? 0),
        ]);
    }

    public function columnFormats(): array
    {
        $formats = [
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];

        // Kolom I (Nilai Perolehan) sampai kolom terakhir = format angka ribuan
        for ($i = self::KOLOM_TETAP; $i <= $this->jumlahKolom(); $i++) {
            $formats[Coordinate::stringFromColumnIndex($i)] = '#,##0';
        }

        return $formats;
    }

    public function styles(Worksheet $sheet): ?array
    {
        $kolomAkhir = Coordinate::stringFromColumnIndex($this->jumlahKolom());

        $sheet->getStyle("A1:{$kolomAkhir}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00617A'], // brand-700
            ],
        ]);

        $sheet->freezePane('A2');

        return [];
    }

    /** 9 kolom tetap + kolom per bulan + 3 kolom total (Total, Akumulasi, Nilai Buku). */
    private function jumlahKolom(): int
    {
        return self::KOLOM_TETAP + count($this->bulanList) + 3;
    }
}