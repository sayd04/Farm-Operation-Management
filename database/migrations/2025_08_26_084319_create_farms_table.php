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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')->constrained('users')->onDelete('cascade')->index();
			$table->string('farm_name');
			$table->string('location');
			$table->decimal('total_area', 10, 2)->default(0);
			$table->string('soil_type')->nullable();
			$table->string('irrigation_type')->nullable();
			$table->decimal('ph_level', 4, 2)->nullable();
			$table->decimal('elevation', 8, 2)->nullable();
			$table->enum('dominant_crop_type', ['rice'])->default('rice');
			$table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};