<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            // Options activées ou non
            $table->boolean('enable_12')->default(false);
            $table->boolean('enable_24')->default(false);
            $table->boolean('enable_cash')->default(false);

            // Prix associés (nullable car parfois pas activé)
            $table->bigInteger('price_12_months')->nullable();
            $table->bigInteger('price_24_months')->nullable();
            $table->bigInteger('price_cash')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'enable_12',
                'enable_24',
                'enable_cash',
                'enable_36',
                'price_12_months',
                'price_24_months',
                'price_36_months',
                'price_cash'
            ]);
        });
    }
};
