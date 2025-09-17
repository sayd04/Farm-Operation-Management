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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
			$table->foreignId('farmer_id')->constrained('users')->onDelete('cascade')->index();
			$table->string('name');
			$table->string('category');
			$table->decimal('quantity', 10, 2)->default(0);
			$table->string('unit');
			$table->decimal('price_per_unit', 10, 2)->default(0);
			$table->decimal('min_stock', 10, 2)->default(0);
			$table->string('sku')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};