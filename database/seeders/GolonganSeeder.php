<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Golongan;
use Illuminate\Database\Seeder;

class GolonganSeeder extends Seeder
{
    public function run(): void
    {
        // persen = 100 / masa manfaat (garis lurus)
        $golongans = [
            ['kode' => '01', 'nama' => 'Golongan I',   'masa_manfaat' => 4,  'persen' => 25.00],
            ['kode' => '02', 'nama' => 'Golongan II',  'masa_manfaat' => 8,  'persen' => 12.50],
            ['kode' => '03', 'nama' => 'Golongan III', 'masa_manfaat' => 10, 'persen' => 10.00],
            ['kode' => '04', 'nama' => 'Golongan IV',  'masa_manfaat' => 16, 'persen' => 6.25],
            ['kode' => '05', 'nama' => 'Golongan V',   'masa_manfaat' => 20, 'persen' => 5.00],
        ];

        foreach ($golongans as $g) {
            Golongan::updateOrCreate(['kode' => $g['kode']], $g);
        }

        // [kode_aktiva_tetap => [sub_jenis => kode golongan]]
        $map = [
            '09' => [
                '15' => '01', '21' => '02', '22' => '02', '24' => '02', '25' => '02',
                '27' => '01', '31' => '01', '40' => '01', '41' => '01', '47' => '02',
                '49' => '01', '59' => '01', '99' => '01',
            ],
            '08' => ['19' => '02'],
            '05' => ['26' => '01', '29' => '01', '90' => '01'],
            '02' => ['58' => '01'],
        ];

        foreach ($map as $kode => $subs) {
            foreach ($subs as $sub => $kodeGolongan) {
                $golongan = Golongan::where('kode', $kodeGolongan)->first();

                $updated = Category::where('kode_aktiva_tetap', $kode)
                    ->where('sub_jenis', $sub)
                    ->update(['golongan_id' => $golongan->id]);

                if ($updated === 0) {
                    $this->command?->warn("Kategori $kode/$sub tidak ditemukan, dilewati.");
                }
            }
        }
    }
}