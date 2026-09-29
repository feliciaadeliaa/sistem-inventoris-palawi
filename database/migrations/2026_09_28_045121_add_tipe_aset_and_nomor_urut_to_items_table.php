<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('items', function (Blueprint $t) {
        $t->unsignedTinyInteger('tipe_aset')->nullable()->after('location_id');
        $t->unsignedSmallInteger('nomor_urut')->nullable()->unique()->after('tipe_aset');
    });
}

public function down(): void
{
    Schema::table('items', function (Blueprint $t) {
        $t->dropUnique(['nomor_urut']);
        $t->dropColumn(['tipe_aset', 'nomor_urut']);
    });
}
};
