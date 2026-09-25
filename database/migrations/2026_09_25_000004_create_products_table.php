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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_profile_id')->constrained('farmer_profiles')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('unit')->default('kg'); // kg, lb, bunch, dozen, box
            $table->decimal('price', 10, 2);
            $table->decimal('zero_waste_price', 10, 2)->nullable();
            $table->boolean('is_zero_waste_active')->default(false);
            $table->integer('stock_quantity')->default(10);
            $table->dateTime('harvest_date')->nullable();
            $table->string('harvest_notes')->nullable();
            $table->decimal('co2_savings_kg', 6, 2)->default(1.50);
            $table->text('description')->nullable();
            $table->string('badge_label')->nullable(); // e.g. "Early Morning Pick", "100% Pesticide-Free"
            $table->enum('status', ['available', 'low_stock', 'sold_out'])->default('available');
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
