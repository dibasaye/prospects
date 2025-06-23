<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->text('description')->nullable();
            $table->decimal('total_area', 10, 2)->nullable();
            $table->integer('total_lots')->default(0);
            $table->decimal('base_price_per_sqm', 10, 2);
            $table->decimal('reservation_fee', 15, 2);
            $table->decimal('membership_fee', 15, 2);
            $table->string('payment_plan')->default('24_months'); // 12_months, 24_months, 36_months
            $table->json('amenities')->nullable(); // JSON array of amenities
            $table->string('status')->default('active'); // active, inactive, sold_out
            $table->string('image_url')->nullable();
            $table->json('gallery_images')->nullable(); // JSON array of image URLs
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};