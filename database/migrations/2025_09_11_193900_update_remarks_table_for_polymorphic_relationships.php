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
        Schema::table('remarks', function (Blueprint $table) {
            // Add polymorphic columns
            $table->string('remarkable_type')->nullable()->after('id');
            $table->unsignedBigInteger('remarkable_id')->nullable()->after('remarkable_type');

            // Rename content to remark for consistency
            $table->renameColumn('content', 'remark');

            // Make donation_id nullable since we're using polymorphic relationships
            $table->unsignedBigInteger('donation_id')->nullable()->change();

            // Add index for polymorphic relationship
            $table->index(['remarkable_type', 'remarkable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remarks', function (Blueprint $table) {
            // Remove polymorphic columns
            $table->dropIndex(['remarkable_type', 'remarkable_id']);
            $table->dropColumn(['remarkable_type', 'remarkable_id']);

            // Rename remark back to content
            $table->renameColumn('remark', 'content');

            // Make donation_id required again
            $table->unsignedBigInteger('donation_id')->nullable(false)->change();
        });
    }
};
