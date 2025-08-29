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
        // Pour MySQL, nous devons modifier l'ENUM existant
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrateur', 'responsable_commercial', 'commercial', 'caissier') DEFAULT 'commercial'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre l'ENUM original
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrateur', 'responsable_commercial', 'commercial') DEFAULT 'commercial'");
    }
}; 