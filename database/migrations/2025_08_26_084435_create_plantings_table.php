<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->string('crop_type');
            $table->datetime('planting_date');
            $table->datetime('expected_harvest_date');
            $table->enum('status', ['planted', 'growing', 'ready', 'harvested', 'failed'])->default('planted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantings');
    }
};