<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('golongans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 2)->unique();               // 01, 02, ...
            $table->string('nama', 50);                        // Golongan I, Golongan II, ...
            $table->unsignedSmallInteger('masa_manfaat');      // tahun
            $table->decimal('persen', 5, 2);                   // tarif penyusutan per tahun (%)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('golongans');
    }
};
