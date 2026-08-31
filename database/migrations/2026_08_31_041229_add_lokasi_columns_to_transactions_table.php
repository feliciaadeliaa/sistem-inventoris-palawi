<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('lokasi_asal_id')->nullable()->after('keterangan')->constrained('locations')->nullOnDelete();
            $table->foreignId('lokasi_tujuan_id')->nullable()->after('lokasi_asal_id')->constrained('locations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['lokasi_asal_id']);
            $table->dropForeign(['lokasi_tujuan_id']);
            $table->dropColumn(['lokasi_asal_id', 'lokasi_tujuan_id']);
        });
    }
};