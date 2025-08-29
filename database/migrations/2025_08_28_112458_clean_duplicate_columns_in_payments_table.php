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
            // Supprimer les champs en double
            $columnsToDrop = [
                'manager_validated',
                'manager_validated_at',
                'caissier_validated_at',
                'caissier_amount_received'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être annulée car elle supprime des doublons
        // La seule façon de revenir en arrière serait de restaurer une sauvegarde
    }
};
