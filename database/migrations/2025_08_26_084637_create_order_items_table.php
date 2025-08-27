<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('mongodb')->create('order_items', function ($collection) {
            $collection->index('order_id');
            $collection->index('inventory_item_id');
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->drop('order_items');
    }
};
