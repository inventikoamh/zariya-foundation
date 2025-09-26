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
        Schema::create('donation_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            $table->foreignId('provided_by')->constrained('users')->onDelete('cascade'); // admin or volunteer who provided
            $table->enum('donation_type', ['monetary', 'materialistic', 'service']);
            $table->decimal('amount', 15, 2)->nullable(); // for monetary donations
            $table->string('currency', 3)->nullable(); // for monetary donations
            $table->decimal('exchange_rate', 10, 4)->nullable(); // for monetary donations
            $table->decimal('converted_amount', 15, 2)->nullable(); // for monetary donations
            $table->string('converted_currency', 3)->nullable(); // for monetary donations
            $table->foreignId('account_id')->nullable()->constrained('accounts')->onDelete('set null'); // for monetary donations
            $table->integer('quantity')->nullable(); // for materialistic donations
            $table->string('unit', 50)->nullable(); // for materialistic donations (kg, pieces, etc.)
            $table->text('description')->nullable(); // for service donations or additional details
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('provided_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['beneficiary_id', 'donation_type']);
            $table->index(['donation_id', 'status']);
            $table->index(['provided_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_histories');
    }
};
