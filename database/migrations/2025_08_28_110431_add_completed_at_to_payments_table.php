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
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('admin_validated_at');
        });
        
        // Mettre à jour les paiements déjà complétés
        DB::table('payments')
            ->where('validation_status', 'completed')
            ->update(['completed_at' => DB::raw('admin_validated_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
