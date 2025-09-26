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
        Schema::create('remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', [
                'status_update',     // Status change remarks
                'assignment',        // Assignment remarks
                'progress',          // Progress update remarks
                'completion',        // Completion remarks
                'cancellation',      // Cancellation remarks
                'general'            // General remarks
            ])->default('general');
            $table->text('content');
            $table->json('metadata')->nullable(); // Store additional data like old_status, new_status, etc.
            $table->boolean('is_internal')->default(false); // Internal notes not visible to donor
            $table->timestamps();
            
            // Indexes
            $table->index(['donation_id', 'created_at']);
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remarks');
    }
};