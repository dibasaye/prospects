<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->string('lot_number');
            $table->decimal('area', 8, 2); // in square meters
            $table->enum('position', ['angle', 'facade', 'interieur']);
            $table->enum('status', ['disponible', 'reserve_temporaire', 'reserve', 'vendu'])->default('disponible');
            $table->decimal('base_price', 15, 2);
            $table->decimal('position_supplement', 15, 2)->default(0);
            $table->decimal('final_price', 15, 2);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->timestamp('reserved_until')->nullable();
            $table->text('description')->nullable();
            $table->json('coordinates')->nullable(); // JSON for plot coordinates on site map
            $table->boolean('has_utilities')->default(false);
            $table->json('features')->nullable(); // JSON array of special features
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('prospects')->onDelete('set null');
            $table->unique(['site_id', 'lot_number']);
            $table->index(['site_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};