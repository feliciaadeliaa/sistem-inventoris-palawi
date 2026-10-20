<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up(): void
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->text('alasan_penolakan')->nullable()->after('keterangan');
        $table->foreignId('ditolak_oleh')->nullable()->after('alasan_penolakan'); // opsional, untuk tahu siapa yang menolak
    });
}

public function down(): void
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->dropColumn(['alasan_penolakan', 'ditolak_oleh']);
    });
}
};
