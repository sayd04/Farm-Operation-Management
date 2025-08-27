<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('mongodb')->create('inventory_items', function ($collection) {
            $collection->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->drop('inventory_items');
    }
};
