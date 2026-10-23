<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Golongan;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan master golongan sudah ada sebelum kategori dibuat
        $this->call(GolonganSeeder::class);

        $golonganIds = Golongan::pluck('id', 'kode');

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

        // Sub jenis yang dipakai sementara.
        // Format: [kode sub jenis, keterangan fungsi, kode golongan]
        $sub = [
            '09' => [
                ['15', 'MESIN KOMP-PRINTER',            '01'],
                ['21', 'Meubel Logam-Filling cabinet',  '02'],
                ['22', 'Meubel Logam-Meja kerja',       '02'],
                ['24', 'Meubel Logam-Meubelair tamu',   '02'],
                ['25', 'Meubel Logam-lainnya',          '02'],
                ['27', 'Meubel kayu-Meja kerja',        '01'],
                ['31', 'Perlngkpan In-Pemotong rumput', '01'],
                ['40', 'P.Elek Radio/Karaoke/Sound',    '01'],
                ['41', 'P.Elek TV',                     '01'],
                ['47', 'P.ELEK-AC',                     '02'],
                ['49', 'P.ELEK-ELEKTRO LAINNYA',        '01'],
                ['59', 'Alat Ukur Lainnya',             '01'],
                ['99', 'Perl. Kantor Lainnya',          '01'],
            ],
            '08' => [
                ['19', 'Kend Barang-Lainnya', '02'],
            ],
            '05' => [
                ['26', 'KOMUNIKASI JARINGAN HT',   '01'],
                ['29', 'KOMUNIKASI-TELEK/JARINGAN', '01'],
                ['90', 'Lain-Sound System',        '01'],
            ],
            '02' => [
                ['58', 'Kursi', '01'],
            ],
        ];

        $n = 0;
        $nextId = function () use (&$n) {
            return 'C' . str_pad(++$n, 2, '0', STR_PAD_LEFT);
        };

        foreach ($jenis as $kode => $nama) {
            // Jenis yang belum punya sub jenis tetap masuk, sub_jenis & golongan dikosongkan
            if (empty($sub[$kode])) {
                Category::create([
                    'category_id'        => $nextId(),
                    'kode_aktiva_tetap'  => $kode,
                    'jenis_aktiva_tetap' => $nama,
                ]);
                continue;
            }

            foreach ($sub[$kode] as [$kodeSub, $keterangan, $kodeGolongan]) {
                $golonganId = $golonganIds[$kodeGolongan] ?? null;

                if ($golonganId === null) {
                    throw new \RuntimeException("Golongan '{$kodeGolongan}' untuk kategori {$kode}/{$kodeSub} tidak ditemukan di master golongan.");
                }

                Category::create([
                    'category_id'        => $nextId(),
                    'kode_aktiva_tetap'  => $kode,
                    'jenis_aktiva_tetap' => $nama,
                    'sub_jenis'          => $kodeSub,
                    'keterangan_fungsi'  => $keterangan,
                    'golongan_id'        => $golonganId,
                ]);
            }
        }
    }
}