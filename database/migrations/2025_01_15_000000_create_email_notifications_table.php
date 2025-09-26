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
        if (!Schema::hasTable('email_notifications')) {
            Schema::create('email_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('type'); // registration, donation, achievement_earned, achievement_created, profile_update, etc.
                $table->string('subject');
                $table->text('body');
                $table->json('data')->nullable(); // Additional data for the email
                $table->string('recipient_email');
                $table->string('recipient_name')->nullable();
                $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
                $table->timestamp('sent_at')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();

                $table->index(['type', 'status']);
                $table->index('recipient_email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notifications');
    }
};
