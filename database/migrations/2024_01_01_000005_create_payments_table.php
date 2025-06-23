<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('site_id');
            $table->unsignedBigInteger('lot_id')->nullable();
            $table->enum('type', ['adhesion', 'reservation', 'mensualite']);
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('cash'); // cash, bank_transfer, mobile_money
            $table->string('reference_number')->unique();
            $table->text('description')->nullable();
            $table->date('payment_date');
            $table->date('due_date')->nullable();
            $table->string('receipt_url')->nullable(); // path to receipt file
            $table->boolean('is_confirmed')->default(false);
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('prospects')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('lot_id')->references('id')->on('lots')->onDelete('set null');
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['client_id', 'type']);
            $table->index(['site_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};