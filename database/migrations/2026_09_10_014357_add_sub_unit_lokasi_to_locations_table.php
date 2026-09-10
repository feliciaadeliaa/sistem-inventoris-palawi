<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::table('locations', function (Blueprint $table) {
        $table->string('sub_unit_bisnis')->nullable()->after('unit_bisnis');
    });
    }

    public function down(): void
    {
    Schema::table('locations', function (Blueprint $table) {
        $table->dropColumn('sub_unit_bisnis');
    });
    }
};