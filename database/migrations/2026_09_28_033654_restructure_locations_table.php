<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buang semua kolom terkait kalau ada (kolom lama + sisa migration yang gagal)
        $drop = array_values(array_filter(
            [
                'nama_lokasi', 'code', 'wilayah', 'sub_unit_bisnis',
                'kode_unit_bisnis', 'unit_bisnis', 'kode_lokasi', 'nama_wisata',
            ],
            fn ($col) => Schema::hasColumn('locations', $col)
        ));

        if ($drop) {
            Schema::table('locations', function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            });
        }

        // 2. Tambah kolom baru
        Schema::table('locations', function (Blueprint $table) {
            $table->char('kode_unit_bisnis', 1)->after('id');
            $table->string('unit_bisnis')->after('kode_unit_bisnis');
            $table->char('kode_lokasi', 2)->unique()->after('unit_bisnis');
            $table->string('nama_wisata')->after('kode_lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropUnique(['kode_lokasi']);
            $table->dropColumn(['kode_unit_bisnis', 'unit_bisnis', 'kode_lokasi', 'nama_wisata']);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->string('nama_lokasi')->nullable();
        });
    }
};