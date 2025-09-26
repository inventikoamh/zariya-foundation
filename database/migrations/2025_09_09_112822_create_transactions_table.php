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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->enum('type', ['income', 'expense', 'transfer', 'donation']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            
            // Account information
            $table->foreignId('from_account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->foreignId('to_account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            
            // Related entities
            $table->foreignId('donation_id')->nullable()->constrained('donations')->onDelete('set null');
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('set null');
            
            // User information
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            
            // Additional fields
            $table->json('metadata')->nullable(); // Store additional transaction data
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'status']);
            $table->index(['from_account_id', 'to_account_id']);
            $table->index('transaction_number');
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
