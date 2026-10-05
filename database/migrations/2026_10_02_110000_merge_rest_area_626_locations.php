<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $rows = DB::table('locations')
                ->where('nama_wisata', 'like', '%626%')
                ->orderBy('kode_lokasi')
                ->get();

            // Sudah digabung sebelumnya
            if ($rows->count() === 1) {
                return;
            }

            if ($rows->count() !== 2) {
                throw new RuntimeException('Ditemukan ' . $rows->count() . ' lokasi 626, seharusnya 2 (A dan B). Dibatalkan.');
            }

            [$keep, $drop] = [$rows[0], $rows[1]]; // A (kode lebih kecil) dipertahankan

            if ($keep->kode_unit_bisnis !== $drop->kode_unit_bisnis) {
                throw new RuntimeException('Lokasi 626 A dan B beda klaster. Dibatalkan.');
            }

            // 1. Nomor aktiva aset di B: segmen ke-4 diganti kode lokasi A
            $items = DB::table('items')->where('location_id', $drop->id)->get(['id', 'nomor_aktiva_tetap']);
            foreach ($items as $item) {
                $parts = explode('.', (string) $item->nomor_aktiva_tetap);
                if (count($parts) === 5) {
                    $parts[3] = $keep->kode_unit_bisnis . $keep->kode_lokasi;
                    DB::table('items')->where('id', $item->id)->update([
                        'nomor_aktiva_tetap' => implode('.', $parts),
                    ]);
                }
            }

            // 2. Pindahkan semua referensi ke tabel lain (mutasi, transaksi, dll.) dari B ke A
            $refs = DB::select(
                "select table_name as t, column_name as c from information_schema.key_column_usage
                 where table_schema = database()
                   and referenced_table_name = 'locations' and referenced_column_name = 'id'"
            );
            foreach ($refs as $ref) {
                DB::table($ref->t)->where($ref->c, $drop->id)->update([$ref->c => $keep->id]);
            }

            // 3. Ganti nama A, hapus B
            DB::table('locations')->where('id', $keep->id)->update(['nama_wisata' => 'Rest Area 626']);
            DB::table('locations')->where('id', $drop->id)->delete();
        });
    }

    public function down(): void
    {
        // Penggabungan data tidak bisa dibalik otomatis.
    }
};
