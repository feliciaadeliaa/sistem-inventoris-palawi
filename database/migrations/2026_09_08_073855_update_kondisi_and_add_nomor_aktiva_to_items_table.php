<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Perbesar dulu ke VARCHAR(20) - cukup untuk nilai lama (rusak_ringan) maupun baru
        DB::statement("ALTER TABLE items MODIFY kondisi VARCHAR(20) NOT NULL DEFAULT 'B'");

        // Konversi nilai lama ke kode baru
        DB::table('items')->where('kondisi', 'baik')->update(['kondisi' => 'B']);
        DB::table('items')->where('kondisi', 'rusak_ringan')->update(['kondisi' => 'BPR']);
        DB::table('items')->where('kondisi', 'rusak_berat')->update(['kondisi' => 'RB']);

        Schema::table('items', function (Blueprint $table) {
            $table->string('nomor_aktiva_tetap')->nullable()->after('golongan_at');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('nomor_aktiva_tetap');
        });

        DB::table('items')->where('kondisi', 'B')->update(['kondisi' => 'baik']);
        DB::table('items')->where('kondisi', 'BPR')->update(['kondisi' => 'rusak_ringan']);
        DB::table('items')->where('kondisi', 'RB')->update(['kondisi' => 'rusak_berat']);
        DB::table('items')->where('kondisi', 'RSS')->update(['kondisi' => 'rusak_berat']);

        DB::statement("ALTER TABLE items MODIFY kondisi ENUM('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik'");
    }
};