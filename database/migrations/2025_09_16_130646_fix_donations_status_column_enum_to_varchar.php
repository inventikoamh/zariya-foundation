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
        // First, let's check if the column is still ENUM
        $columnInfo = DB::select("SHOW COLUMNS FROM donations WHERE Field = 'status'");

        if (!empty($columnInfo) && strpos($columnInfo[0]->Type, 'enum') !== false) {
            // Drop the existing status column and recreate as VARCHAR
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn('status');
            });

            // Recreate as VARCHAR
            Schema::table('donations', function (Blueprint $table) {
                $table->string('status', 50)->default('pending')->after('details');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'assigned',
                'in_progress',
                'completed',
                'cancelled',
                'rejected'
            ])->default('pending')->after('details');
        });
    }
};
