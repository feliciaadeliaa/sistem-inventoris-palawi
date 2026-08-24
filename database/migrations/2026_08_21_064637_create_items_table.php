<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_id')->nullable()->unique(); // basis QR, digenerate setelah insert

            $table->string('nama_barang');
            $table->string('category_id', 5);
            $table->foreignId('location_id')->constrained('locations'); // lokasi pemakaian

            // Atribut tambahan (klasifikasi aset)
            $table->string('golongan_at', 10);
            $table->unsignedSmallInteger('tahun_perolehan');
            $table->unsignedSmallInteger('masa_manfaat'); // dalam tahun
            $table->decimal('nilai_perolehan', 15, 2);

            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->date('tanggal_terima');

            // Status operasional & soft-status
            $table->enum('status', ['tersedia', 'dipinjam', 'dalam_perbaikan', 'nonaktif'])->default('tersedia');
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('category_id')->references('category_id')->on('categories');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};