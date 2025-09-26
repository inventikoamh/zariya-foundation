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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Status name (e.g., 'pending', 'completed')
            $table->string('display_name'); // Display name (e.g., 'Pending', 'Completed')
            $table->string('type'); // Type: 'donation', 'beneficiary', 'materialistic', 'service'
            $table->string('color', 7)->default('#6B7280'); // Hex color for UI
            $table->boolean('is_fixed')->default(false); // Fixed statuses cannot be deleted
            $table->boolean('is_active')->default(true); // Active statuses
            $table->integer('sort_order')->default(0); // For ordering
            $table->text('description')->nullable(); // Description of the status
            $table->timestamps();

            // Indexes
            $table->index(['type', 'is_active']);
            $table->unique(['name', 'type']); // Unique name per type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
