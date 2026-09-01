<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('gm_approved_by')->nullable()->constrained('users');
            $table->timestamp('gm_approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['gm_approved_by']);
            $table->dropColumn(['gm_approved_by', 'gm_approved_at']);
        });
    }
};