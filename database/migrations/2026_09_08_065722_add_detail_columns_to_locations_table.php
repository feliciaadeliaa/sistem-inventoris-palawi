<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('kode_lokasi')->nullable()->unique()->after('nama_lokasi');
            $table->string('wilayah')->nullable()->after('kode_lokasi');
            $table->string('unit_bisnis')->nullable()->after('wilayah');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['kode_lokasi', 'wilayah', 'unit_bisnis']);
        });
    }
};