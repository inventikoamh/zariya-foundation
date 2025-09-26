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
        // First, update any existing records with invalid types to 'bank'
        DB::table('accounts')
            ->whereNotIn('type', ['bank', 'cash'])
            ->update(['type' => 'bank']);

        // Then modify the column to only allow 'bank' and 'cash'
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('type', ['bank', 'cash'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('type', ['bank', 'cash', 'digital', 'investment', 'other'])->change();
        });
    }
};