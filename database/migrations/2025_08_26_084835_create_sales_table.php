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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('harvest_id')->constrained()->onDelete('cascade');
			$table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
			$table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
			$table->decimal('quantity_kg', 10, 2);
            $table->decimal('total_amount', 10, 2);
			$table->date('sale_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};