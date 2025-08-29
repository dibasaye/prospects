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
            // Première validation par le caissier
            if (!Schema::hasColumn('payments', 'caissier_validated')) {
                $table->boolean('caissier_validated')->default(false);
            }
            if (!Schema::hasColumn('payments', 'caissier_validated_by')) {
                $table->unsignedBigInteger('caissier_validated_by')->nullable();
            }
            if (!Schema::hasColumn('payments', 'caissier_validated_at')) {
                $table->timestamp('caissier_validated_at')->nullable();
            }
            if (!Schema::hasColumn('payments', 'caissier_notes')) {
                $table->text('caissier_notes')->nullable();
            }
            if (!Schema::hasColumn('payments', 'caissier_amount_received')) {
                $table->decimal('caissier_amount_received', 15, 2)->nullable();
            }

            // Deuxième validation par le responsable commercial
            if (!Schema::hasColumn('payments', 'manager_validated')) {
                $table->boolean('manager_validated')->default(false);
            }
            if (!Schema::hasColumn('payments', 'manager_validated_by')) {
                $table->unsignedBigInteger('manager_validated_by')->nullable();
            }
            if (!Schema::hasColumn('payments', 'manager_validated_at')) {
                $table->timestamp('manager_validated_at')->nullable();
            }
            if (!Schema::hasColumn('payments', 'manager_notes')) {
                $table->text('manager_notes')->nullable();
            }

            // Statut global de validation
            if (!Schema::hasColumn('payments', 'validation_status')) {
                $table->enum('validation_status', ['pending', 'caissier_validated', 'fully_validated', 'rejected'])->default('pending');
            }
        });

        // Ajouter les clés étrangères après avoir créé les colonnes
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'caissier_validated_by')) {
                $table->foreign('caissier_validated_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('payments', 'manager_validated_by')) {
                $table->foreign('manager_validated_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['caissier_validated_by']);
            $table->dropForeign(['manager_validated_by']);
            $table->dropColumn([
                'caissier_validated',
                'caissier_validated_by',
                'caissier_validated_at',
                'caissier_notes',
                'caissier_amount_received',
                'manager_validated',
                'manager_validated_by',
                'manager_validated_at',
                'manager_notes',
                'validation_status'
            ]);
        });
    }
};
