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
        Schema::create('follow_up_actions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->enum('type', ['appel', 'whatsapp', 'rdv', 'email']);
    $table->text('notes')->nullable();
    $table->timestamp('action_date')->useCurrent();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_actions');
    }
};
