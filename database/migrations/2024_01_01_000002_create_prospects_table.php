<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('phone_secondary')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('id_document')->nullable(); // path to uploaded document
            $table->string('representative_name')->nullable();
            $table->string('representative_phone')->nullable();
            $table->text('representative_address')->nullable();
            $table->enum('status', ['nouveau', 'en_relance', 'interesse', 'converti', 'abandonne'])->default('nouveau');
            $table->unsignedBigInteger('assigned_to_id')->nullable();
            $table->unsignedBigInteger('interested_site_id')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->date('contact_date')->nullable();
            $table->date('next_follow_up')->nullable();
            $table->json('preferences')->nullable(); // JSON field for additional preferences
            $table->timestamps();

            $table->foreign('assigned_to_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'assigned_to_id']);
            $table->index('contact_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};