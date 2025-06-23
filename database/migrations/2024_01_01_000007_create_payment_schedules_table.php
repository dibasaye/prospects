<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->integer('installment_number');
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->boolean('is_paid')->default(false);
            $table->date('paid_date')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable(); // links to actual payment
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            $table->unique(['contract_id', 'installment_number']);
            $table->index(['contract_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};