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
        Schema::create('weather_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
			$table->decimal('temperature_c', 5, 1)->nullable();
			$table->decimal('humidity_pct', 5, 1)->nullable();
			$table->decimal('wind_speed_mps', 5, 1)->nullable();
			$table->string('conditions')->nullable();
			$table->json('forecast_json')->nullable();
			$table->datetime('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_logs');
    }
};