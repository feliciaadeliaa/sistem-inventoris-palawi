<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemImportTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'nama_barang', 'kode_aktiva_tetap', 'sub_jenis', 'tipe_aset',
            'kode_klaster', 'kode_lokasi', 'tahun_perolehan', 'tanggal_terima',
            'nilai_perolehan', 'kondisi', 'golongan_at', 'masa_manfaat',
        ];
    }

    public function array(): array
    {
        return [
            ['Laptop Asus', '09', '01', 'AT', '1', '01', 2026, '2026-03-15', 12500000, 'B', '', ''],
        ];
    }
}