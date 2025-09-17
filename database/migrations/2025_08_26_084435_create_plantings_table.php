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
        Schema::create('plantings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
			$table->string('rice_variety');
			$table->enum('crop_type', ['rice'])->default('rice');
			$table->string('planting_method')->nullable();
			$table->date('planting_date')->index();
			$table->date('expected_harvest_date')->nullable()->index();
			$table->enum('status', ['planned','growing','harvested','abandoned'])->default('planned');
			$table->decimal('seed_rate', 10, 2)->nullable();
			$table->json('fertilizer_plan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantings');
    }
};