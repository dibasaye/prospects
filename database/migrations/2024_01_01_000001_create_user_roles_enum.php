<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enum_demo', function (Blueprint $table) {
            $table->id();

            // ENUM des rôles utilisateurs
            $table->enum('user_role', [
                'administrateur',
                'responsable_commercial',
                'commercial'
            ])->nullable();

            // ENUM pour le statut du prospect
            $table->enum('prospect_status', [
                'nouveau',
                'en_relance',
                'interesse',
                'converti',
                'abandonne'
            ])->nullable();

            // ENUM pour le statut du lot
            $table->enum('lot_status', [
                'disponible',
                'reserve_temporaire',
                'reserve',
                'vendu'
            ])->nullable();

            // ENUM pour la position du lot
            $table->enum('lot_position', [
                'angle',
                'facade',
                'interieur'
            ])->nullable();

            // ENUM pour le type de paiement
            $table->enum('payment_type', [
                'adhesion',
                'reservation',
                'mensualite'
            ])->nullable();

            // ENUM pour le statut du contrat
            $table->enum('contract_status', [
                'brouillon',
                'genere',
                'signe',
                'archive'
            ])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enum_demo');
    }
};
