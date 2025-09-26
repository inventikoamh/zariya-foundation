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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['monetary', 'materialistic', 'service']);
            $table->json('details'); // Store type-specific details as JSON
            $table->enum('status', [
                'pending',           // Initial status when submitted
                'assigned',          // Assigned to a volunteer
                'in_progress',       // Volunteer is working on it
                'completed',         // Successfully completed
                'cancelled',         // Cancelled by donor or admin
                'rejected'           // Rejected by volunteer/admin
            ])->default('pending');
            
            // Donor information
            $table->foreignId('donor_id')->constrained('users')->onDelete('cascade');
            
            // Location information for auto-routing
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->string('pincode')->nullable();
            $table->text('address')->nullable();
            
            // Assignment information
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            
            // Completion information
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            
            // Additional fields
            $table->text('notes')->nullable(); // General notes
            $table->boolean('is_urgent')->default(false);
            $table->integer('priority')->default(1); // 1=Low, 2=Medium, 3=High, 4=Critical
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['type', 'status']);
            $table->index(['donor_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['country_id', 'state_id', 'city_id']);
            $table->index(['is_urgent', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};