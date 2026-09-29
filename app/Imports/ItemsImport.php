<?php
// app/Imports/ItemsImport.php
namespace App\Imports;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ItemsImport implements ToCollection, WithHeadingRow
{
public function collection(Collection $collection): void
{
    $rows = $collection;
    $errors = [];

        foreach ($rows as $i => $row) {
            $baris = $i + 2;
            if (blank($row['nama_barang'])) continue;

            $kode    = str_pad(trim($row['kode_aktiva_tetap']), 2, '0', STR_PAD_LEFT);
            $sub     = str_pad(trim($row['sub_jenis']), 2, '0', STR_PAD_LEFT);
            $klaster = trim($row['kode_klaster']);
            $wisata  = str_pad(trim($row['kode_lokasi']), 2, '0', STR_PAD_LEFT);
            $tipe    = strtoupper(trim($row['tipe_aset']));

            // SESUAIKAN nama kolom kategori & lokasi
            $category = Category::where('kode_aktiva_tetap', $kode)->where('sub_jenis', $sub)->first();
            $location = Location::where('kode_unit_bisnis', $klaster)->where('kode_lokasi', $wisata)->first();

            if (!$category) $errors[] = "Baris $baris: kategori $kode/$sub tidak ditemukan";
            if (!$location) $errors[] = "Baris $baris: lokasi $klaster/$wisata tidak ditemukan";
            if (!in_array($tipe, ['AT', 'IBAT'])) $errors[] = "Baris $baris: tipe_aset harus AT atau IBAT";
            if (!preg_match('/^\d{4}$/', (string) $row['tahun_perolehan'])) {
                $errors[] = "Baris $baris: tahun_perolehan harus 4 digit";
            }
            if (!is_numeric($row['nilai_perolehan'])) {
                $errors[] = "Baris $baris: nilai_perolehan harus angka";
            }

            // Tanggal: bisa berupa serial Excel atau teks
            try {
                $tanggal = is_numeric($row['tanggal_terima'])
                    ? Carbon::instance(ExcelDate::excelToDateTimeObject($row['tanggal_terima']))
                    : Carbon::parse($row['tanggal_terima']);
            } catch (\Throwable $e) {
                $tanggal = null;
                $errors[] = "Baris $baris: tanggal_terima tidak valid";
            }

            // Golongan & masa manfaat: Excel dulu, fallback ke kategori
            $golongan = filled($row['golongan_at'] ?? null) ? $row['golongan_at'] : ($category->golongan_at ?? null);
            $masa     = filled($row['masa_manfaat'] ?? null) ? $row['masa_manfaat'] : ($category->masa_manfaat ?? null);
            if (blank($golongan) || blank($masa)) {
                $errors[] = "Baris $baris: golongan_at/masa_manfaat kosong dan belum diisi di kategori";
            }

            $prepared[] = compact('row', 'category', 'location', 'tipe', 'tanggal', 'golongan', 'masa');
        }

        if ($errors) {
            throw ValidationException::withMessages(['file' => $errors]);
        }

        DB::transaction(function () use ($prepared) {
            $urut = (int) Item::lockForUpdate()->max('nomor_urut');

            foreach ($prepared as $p) {
                $urut++;
                $row = $p['row'];

                $nomor = sprintf(
                    '%04d.%d.%s%s.%s%s.%s',
                    $urut,
                    $p['tipe'] === 'AT' ? 1 : 2,
                    str_pad($p['category']->kode_aktiva_tetap, 2, '0', STR_PAD_LEFT),
                    str_pad($p['category']->sub_jenis, 2, '0', STR_PAD_LEFT),
                    $p['location']->kode_unit_bisnis,          // SESUAIKAN (klaster)
                    str_pad($p['location']->kode_lokasi, 2, '0', STR_PAD_LEFT),
                    $row['tahun_perolehan']
                );

                Item::create([
                    'item_id'            => $this->generateItemId($urut), // SESUAIKAN
                    'nama_barang'        => $row['nama_barang'],
                    'category_id'        => $p['category']->category_id,
                    'location_id'        => $p['location']->id,
                    'tipe_aset'          => $p['tipe'] === 'AT' ? 1 : 2,
                    'nomor_urut'         => $urut,
                    'golongan_at'        => $p['golongan'],
                    'nomor_aktiva_tetap' => $nomor,
                    'tahun_perolehan'    => $row['tahun_perolehan'],
                    'masa_manfaat'       => $p['masa'],
                    'nilai_perolehan'    => $row['nilai_perolehan'],
                    'kondisi'            => $row['kondisi'] ?? 'B',
                    'tanggal_terima'     => $p['tanggal']->toDateString(),
                    'status'             => 'tersedia',
                    'is_active'          => 1,
                ]);
            }
        });
    }

    private function generateItemId(int $urut): string
    {
        // Ganti dengan logika item_id yang sudah dipakai di ItemController@store
        return 'ITM' . str_pad($urut, 5, '0', STR_PAD_LEFT);
    }
}