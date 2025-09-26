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
        Schema::create('volunteer_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('assignment_type', ['country', 'state', 'city']);
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('state_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('role', ['head_volunteer', 'volunteer'])->default('volunteer');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Note: Unique constraint for head volunteers will be handled at application level
            // due to nullable foreign keys

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['assignment_type', 'country_id', 'state_id', 'city_id'], 'va_assignment_region_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_assignments');
    }
};
