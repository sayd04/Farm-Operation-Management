<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('mongodb')->create('sales', function ($collection) {
            $collection->index('harvest_id');
            $collection->index('buyer_id');
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->drop('sales');
    }
};
