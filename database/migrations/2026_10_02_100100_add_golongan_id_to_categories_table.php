<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('golongan_id')
                ->nullable()
                ->after('keterangan_fungsi')
                ->constrained('golongans')
                ->restrictOnDelete();
        });

        // Kolom lama (golongan & masa manfaat langsung di kategori) digantikan relasi ke golongans.
        Schema::table('categories', function (Blueprint $table) {
            foreach (['golongan_at', 'masa_manfaat'] as $col) {
                if (Schema::hasColumn('categories', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('golongan_id');
            $table->string('golongan_at', 10)->nullable();
            $table->unsignedSmallInteger('masa_manfaat')->nullable();
        });
    }
};
