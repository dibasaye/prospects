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
            // Supprimer les anciens champs de validation s'ils existent
            $columns = [
                'caissier_validated',
                'caissier_validated_by',
                'caissier_validated_at',
                'caissier_notes',
                'caissier_amount_received',
                'manager_validated',
                'manager_validated_by',
                'manager_validated_at',
                'manager_notes',
                'validation_status',
                'validation_proof_path',
                'payment_proof_path'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Ajouter les nouveaux champs pour la validation en 4 étapes
            $table->enum('validation_status', [
                'pending', 
                'caissier_validated', 
                'responsable_validated', 
                'admin_validated',
                'completed',
                'rejected'
            ])->default('pending');

            // Étape 1: Validation par le caissier
            $table->boolean('caissier_validated')->default(false);
            $table->unsignedBigInteger('caissier_validated_by')->nullable();
            $table->timestamp('caissier_validated_at')->nullable();
            $table->text('caissier_notes')->nullable();
            $table->decimal('caissier_amount_received', 15, 2)->nullable();
            $table->string('payment_proof_path')->nullable();

            // Étape 2: Validation par le responsable
            $table->boolean('responsable_validated')->default(false);
            $table->unsignedBigInteger('responsable_validated_by')->nullable();
            $table->timestamp('responsable_validated_at')->nullable();
            $table->text('responsable_notes')->nullable();

            // Étape 3: Validation par l'administrateur
            $table->boolean('admin_validated')->default(false);
            $table->unsignedBigInteger('admin_validated_by')->nullable();
            $table->timestamp('admin_validated_at')->nullable();
            $table->text('admin_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Supprimer les nouveaux champs
            $columns = [
                'validation_status',
                'caissier_validated',
                'caissier_validated_by',
                'caissier_validated_at',
                'caissier_notes',
                'caissier_amount_received',
                'payment_proof_path',
                'responsable_validated',
                'responsable_validated_by',
                'responsable_validated_at',
                'responsable_notes',
                'admin_validated',
                'admin_validated_by',
                'admin_validated_at',
                'admin_notes'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
