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
    DB::table('items')->where('at_ibat', 'I')->update(['at_ibat' => 'AT']);
    DB::table('items')->where('at_ibat', 'II')->update(['at_ibat' => 'IBAT']);
}

public function down(): void
{
    DB::table('items')->where('at_ibat', 'AT')->update(['at_ibat' => 'I']);
    DB::table('items')->where('at_ibat', 'IBAT')->update(['at_ibat' => 'II']);
}
};
