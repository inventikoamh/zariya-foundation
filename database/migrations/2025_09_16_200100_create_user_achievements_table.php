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
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained('achievements')->onDelete('cascade');
            $table->timestamp('earned_at'); // When the achievement was earned
            $table->json('metadata')->nullable(); // Additional data about how it was earned
            $table->boolean('is_notified')->default(false); // Whether user has been notified
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'earned_at']);
            $table->index(['achievement_id', 'earned_at']);
            $table->index('is_notified');

            // Unique constraint to prevent duplicate achievements (unless repeatable)
            $table->unique(['user_id', 'achievement_id', 'earned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};
