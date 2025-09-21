<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('mongodb')->create('weather_logs', function ($collection) {
            $collection->index('field_id');
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->drop('weather_logs');
    }
};
