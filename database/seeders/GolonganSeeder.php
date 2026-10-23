<?php

namespace Database\Seeders;

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
    }
}