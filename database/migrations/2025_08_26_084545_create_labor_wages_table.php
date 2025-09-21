<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('mongodb')->create('labor_wages', function ($collection) {
            $collection->index('laborer_id');
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->drop('labor_wages');
    }
};
