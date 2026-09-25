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
        Schema::create('farmer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('stall_name');
            $table->string('slug')->unique();
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->string('stall_number')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('long', 10, 7)->nullable();
            $table->string('farm_origin')->default('Local Regional Farm');
            $table->string('certifications')->nullable(); // e.g. "Certified Organic, Regenerative Soil Verified"
            $table->decimal('carbon_score_rating', 3, 1)->default(4.8); // 1.0 to 5.0
            $table->string('phone_contact')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmer_profiles');
    }
};
