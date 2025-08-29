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
        Schema::table('payment_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_schedules', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paid_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('payment_schedules', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
}; 