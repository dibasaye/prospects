<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour les valeurs existantes
        DB::statement("ALTER TABLE payments MODIFY COLUMN validation_status ENUM('pending', 'caissier_validated', 'responsable_validated', 'admin_validated', 'completed', 'rejected', 'fully_validated') DEFAULT 'pending'");
        
        // Mettre à jour les anciennes valeurs 'fully_validated' vers 'completed'
        DB::table('payments')
            ->where('validation_status', 'fully_validated')
            ->update(['validation_status' => 'completed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
