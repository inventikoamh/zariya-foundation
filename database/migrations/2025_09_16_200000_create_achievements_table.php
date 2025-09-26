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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Achievement name
            $table->text('description'); // Achievement description
            $table->string('type'); // Type: 'donation', 'volunteer', 'general'
            $table->string('category'); // Category: 'monetary', 'materialistic', 'service', 'completion', 'milestone', etc.
            $table->string('icon_image'); // Path to achievement icon/badge image
            $table->json('criteria'); // Achievement criteria (conditions to unlock)
            $table->integer('points')->default(0); // Points awarded for this achievement
            $table->string('rarity')->default('common'); // common, uncommon, rare, epic, legendary
            $table->boolean('is_active')->default(true); // Whether this achievement is active
            $table->boolean('is_repeatable')->default(false); // Whether this achievement can be earned multiple times
            $table->integer('max_earnings')->nullable(); // Maximum times this achievement can be earned (null = unlimited)
            $table->timestamp('available_from')->nullable(); // When this achievement becomes available
            $table->timestamp('available_until')->nullable(); // When this achievement expires
            $table->timestamps();

            // Indexes
            $table->index(['type', 'category']);
            $table->index(['is_active', 'available_from', 'available_until']);
            $table->index('rarity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
