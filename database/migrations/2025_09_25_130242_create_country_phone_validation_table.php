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
        Schema::create('country_phone_validations', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3)->unique(); // ISO country code (IND, USA, etc.)
            $table->string('country_name');
            $table->string('phone_code'); // +91, +1, etc.
            $table->integer('min_digits')->default(10); // Minimum phone digits
            $table->integer('max_digits')->default(15); // Maximum phone digits
            $table->string('phone_format')->nullable(); // Format example: "XXXX-XXX-XXXX"
            $table->text('validation_regex')->nullable(); // Regex pattern for validation
            $table->text('description')->nullable(); // Description of phone format
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['country_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_phone_validations');
    }
};