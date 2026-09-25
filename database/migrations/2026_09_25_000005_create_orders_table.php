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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('farmer_profile_id')->constrained('farmer_profiles')->onDelete('cascade');
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->string('pickup_slot'); // e.g. "Saturday 08:30 AM - 10:00 AM"
            $table->date('pickup_date')->nullable();
            $table->string('qr_code_hash')->unique();
            $table->string('pickup_token', 8); // 4-digit or 6-digit confirmation pin e.g. "8492"
            $table->enum('status', ['placed', 'accepted', 'ready', 'completed', 'cancelled'])->default('placed');
            $table->string('payment_method')->default('cash_on_pickup');
            $table->string('payment_status')->default('pending_settlement'); // 'pending_settlement', 'settled'
            $table->text('notes')->nullable();
            $table->decimal('co2_saved_total', 6, 2)->default(0.00);
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
