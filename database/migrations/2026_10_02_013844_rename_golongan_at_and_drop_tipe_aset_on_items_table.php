<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{
    Schema::table('items', function (Blueprint $table) {
        $table->renameColumn('golongan_at', 'at_ibat');
        $table->dropColumn('tipe_aset');
    });
}

public function down(): void
{
    Schema::table('items', function (Blueprint $table) {
        $table->renameColumn('at_ibat', 'golongan_at');
        $table->string('tipe_aset')->nullable(); // sesuaikan tipe aslinya
    });
}
};
