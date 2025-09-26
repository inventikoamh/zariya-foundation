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
        Schema::table('donations', function (Blueprint $table) {
            // Change status column from ENUM to VARCHAR to support dynamic statuses
            $table->string('status', 50)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Revert back to ENUM with original statuses
            $table->enum('status', [
                'pending',
                'assigned',
                'in_progress',
                'completed',
                'cancelled',
                'rejected'
            ])->default('pending')->change();
        });
    }
};
