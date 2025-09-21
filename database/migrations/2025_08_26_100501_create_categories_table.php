<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('mongodb')->create('categories');
    }

    public function down(): void
    {
        Schema::connection('mongodb')->drop('categories');
    }
};
