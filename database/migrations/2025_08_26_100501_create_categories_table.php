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
        // No longer needed; inventory uses simple category string. Keep file to avoid renumbering.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to drop
    }
};