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
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['expense_id']);
            
            // Recreate it with cascade delete
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the cascade foreign key constraint
            $table->dropForeign(['expense_id']);
            
            // Recreate it with set null (original behavior)
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('set null');
        });
    }
};