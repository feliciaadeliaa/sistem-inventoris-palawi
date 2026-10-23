<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user','gm','senior_analis','finance') NULL DEFAULT 'user'");
}

public function down(): void
{
    // Pastikan tidak ada user ber-role finance sebelum rollback
    DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user','gm','senior_analis') NULL DEFAULT 'user'");
}
};
