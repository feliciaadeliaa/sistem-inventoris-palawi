<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['category_id' => 'C01', 'nama_kategori' => 'Elektronik'],
            ['category_id' => 'C02', 'nama_kategori' => 'Furnitur'],
            ['category_id' => 'C03', 'nama_kategori' => 'Alat Tulis Kantor'],
            ['category_id' => 'C04', 'nama_kategori' => 'Kendaraan Operasional'],
            ['category_id' => 'C05', 'nama_kategori' => 'Peralatan Lapangan'],
        ]);
    }
}