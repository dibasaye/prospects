<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create ENUM type for user roles
        DB::statement("CREATE TYPE user_role AS ENUM ('administrateur', 'responsable_commercial', 'commercial')");
        
        // Create ENUM type for prospect status
        DB::statement("CREATE TYPE prospect_status AS ENUM ('nouveau', 'en_relance', 'interesse', 'converti', 'abandonne')");
        
        // Create ENUM type for lot status
        DB::statement("CREATE TYPE lot_status AS ENUM ('disponible', 'reserve_temporaire', 'reserve', 'vendu')");
        
        // Create ENUM type for lot position
        DB::statement("CREATE TYPE lot_position AS ENUM ('angle', 'facade', 'interieur')");
        
        // Create ENUM type for payment type
        DB::statement("CREATE TYPE payment_type AS ENUM ('adhesion', 'reservation', 'mensualite')");
        
        // Create ENUM type for contract status
        DB::statement("CREATE TYPE contract_status AS ENUM ('brouillon', 'genere', 'signe', 'archive')");
    }

    public function down(): void
    {
        DB::statement('DROP TYPE IF EXISTS contract_status');
        DB::statement('DROP TYPE IF EXISTS payment_type');
        DB::statement('DROP TYPE IF EXISTS lot_position');
        DB::statement('DROP TYPE IF EXISTS lot_status');
        DB::statement('DROP TYPE IF EXISTS prospect_status');
        DB::statement('DROP TYPE IF EXISTS user_role');
    }
};