<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('mongodb')->create('buyers');
    }

    public function down(): void
    {
        Schema::connection('mongodb')->drop('buyers');
    }
};
