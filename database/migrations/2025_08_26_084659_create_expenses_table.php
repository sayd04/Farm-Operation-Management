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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
			$table->foreignId('farmer_id')->constrained('users')->onDelete('cascade')->index();
			$table->string('description');
            $table->decimal('amount', 10, 2);
            $table->enum('category', ['seeds', 'fertilizer', 'pesticide', 'labor', 'equipment', 'utilities', 'maintenance', 'other']);
			$table->date('date');
            $table->foreignId('planting_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};