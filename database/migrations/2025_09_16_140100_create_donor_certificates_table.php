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
        Schema::create('donor_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            $table->foreignId('certificate_id')->constrained('certificates')->onDelete('cascade');
            $table->foreignId('donor_id')->constrained('users')->onDelete('cascade');
            $table->string('generated_image'); // Path to the generated certificate image
            $table->string('donor_name'); // Donor name as it appears on certificate
            $table->decimal('amount', 15, 2)->nullable(); // Amount for monetary donations
            $table->string('currency', 3)->nullable(); // Currency for monetary donations
            $table->date('donation_date'); // Date of the donation
            $table->string('certificate_number')->unique(); // Unique certificate number
            $table->boolean('is_downloaded')->default(false); // Whether donor has downloaded it
            $table->timestamp('downloaded_at')->nullable(); // When it was downloaded
            $table->timestamps();

            // Indexes
            $table->index(['donor_id', 'created_at']);
            $table->index('certificate_number');
            $table->index('is_downloaded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_certificates');
    }
};
