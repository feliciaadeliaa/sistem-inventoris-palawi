<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('jenis_transaksi'); // stock_in, stock_out, mutasi, permintaan_perbaikan, laporan_kerusakan
            $table->string('status')->default('selesai');
            // selesai              -> default utk Stock In & Mutasi (tanpa approval)
            // menunggu_approval    -> Stock Out & Permintaan Perbaikan saat diajukan
            // disetujui            -> Admin approve Stock Out (barang jadi dipinjam)
            // ditolak              -> Admin tolak pengajuan
            // dikembalikan         -> barang Stock Out sudah dikembalikan
            $table->text('keterangan')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->date('tanggal_kembali_estimasi')->nullable();
            $table->date('tanggal_kembali_aktual')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};