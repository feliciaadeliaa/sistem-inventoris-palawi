<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('nama_kategori', 'keterangan_fungsi');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('keterangan_fungsi')->nullable()->change();
            $table->char('kode_aktiva_tetap', 2)->after('category_id');
            $table->string('jenis_aktiva_tetap')->after('kode_aktiva_tetap');
            $table->char('sub_jenis', 2)->nullable()->after('jenis_aktiva_tetap');

            $table->unique(['kode_aktiva_tetap', 'sub_jenis']);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['kode_aktiva_tetap', 'sub_jenis']);
            $table->dropColumn(['kode_aktiva_tetap', 'jenis_aktiva_tetap', 'sub_jenis']);
            $table->renameColumn('keterangan_fungsi', 'nama_kategori');
        });
    }
};