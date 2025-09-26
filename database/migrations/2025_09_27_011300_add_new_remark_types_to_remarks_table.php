<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new remark types to the ENUM
        DB::statement("ALTER TABLE remarks MODIFY COLUMN type ENUM(
            'status_update',
            'assignment',
            'progress',
            'completion',
            'cancellation',
            'general',
            'priority_change',
            'urgent_change'
        ) DEFAULT 'general'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the new remark types from the ENUM
        DB::statement("ALTER TABLE remarks MODIFY COLUMN type ENUM(
            'status_update',
            'assignment',
            'progress',
            'completion',
            'cancellation',
            'general'
        ) DEFAULT 'general'");
    }
};
