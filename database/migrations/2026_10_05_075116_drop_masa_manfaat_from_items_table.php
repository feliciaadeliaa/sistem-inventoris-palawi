<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('masa_manfaat');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedSmallInteger('masa_manfaat')->default(0)->after('tahun_perolehan');
        });
    }
};