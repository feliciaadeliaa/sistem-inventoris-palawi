<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            '01' => 'Tanah',
            '02' => 'Bangunan Gedung',
            '03' => 'Jalan mobil',
            '04' => 'Jalan Rel',
            '05' => 'Bengkel & Instalasi',
            '06' => 'Tempat Penimbunan',
            '07' => 'Industri',
            '08' => 'Kend Bermotor & Alat Berat',
            '09' => 'Perlgk Kantor & Kend Tak Bmt',
            '10' => 'Kain Seragam',
            '19' => 'Komputer',
            '20' => 'Rumah',
            '99' => 'Rak',
        ];

        // Sub jenis yang dipakai sementara
        $sub = [
            '09' => [
                ['15', 'MESIN KOMP-PRINTER'],
                ['21', 'Meubel Logam-Filling cabinet'],
                ['22', 'Meubel Logam-Meja kerja'],
                ['24', 'Meubel Logam-Meubelair tamu'],
                ['25', 'Meubel Logam-lainnya'],
                ['27', 'Meubel kayu-Meja kerja'],
                ['31', 'Perlngkpan In-Pemotong rumput'],
                ['40', 'P.Elek Radio/Karaoke/Sound'],
                ['41', 'P.Elek TV'],
                ['47', 'P.ELEK-AC'],
                ['49', 'P.ELEK-ELEKTRO LAINNYA'],
                ['59', 'Alat Ukur Lainnya'],
                ['99', 'Perl. Kantor Lainnya'],
            ],
            '08' => [
                ['19', 'Kend Barang-Lainnya'],
            ],
            '05' => [
                ['26', 'KOMUNIKASI JARINGAN HT'],
                ['29', 'KOMUNIKASI-TELEK/JARINGAN'],
                ['90', 'Lain-Sound System'],
            ],
            '02' => [
                ['58', 'Kursi'],
            ],
        ];

        $n = 0;
        $nextId = function () use (&$n) {
            return 'C' . str_pad(++$n, 2, '0', STR_PAD_LEFT);
        };

        foreach ($jenis as $kode => $nama) {
            // Jenis yang belum punya sub jenis tetap masuk, sub_jenis dikosongkan
            if (empty($sub[$kode])) {
                Category::create([
                    'category_id'        => $nextId(),
                    'kode_aktiva_tetap'  => $kode,
                    'jenis_aktiva_tetap' => $nama,
                ]);
                continue;
            }

            foreach ($sub[$kode] as [$kodeSub, $keterangan]) {
                Category::create([
                    'category_id'        => $nextId(),
                    'kode_aktiva_tetap'  => $kode,
                    'jenis_aktiva_tetap' => $nama,
                    'sub_jenis'          => $kodeSub,
                    'keterangan_fungsi'  => $keterangan,
                ]);
            }
        }
    }
}