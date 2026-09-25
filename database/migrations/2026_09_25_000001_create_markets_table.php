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
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address');
            $table->string('city')->default('Metropolitan Region');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('operating_days'); // e.g. "Wednesday, Saturday, Sunday"
            $table->string('timing'); // e.g. "07:00 AM - 02:00 PM"
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'seasonal', 'inactive'])->default('active');
            $table->integer('stall_capacity')->default(25);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
