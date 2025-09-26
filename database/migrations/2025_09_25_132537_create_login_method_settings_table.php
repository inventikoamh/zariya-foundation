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
        if (!Schema::hasTable('login_method_settings')) {
            Schema::create('login_method_settings', function (Blueprint $table) {
                $table->id();
                $table->string('method')->unique(); // 'password', 'sms', 'both'
                $table->boolean('is_enabled')->default(true);
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->json('settings')->nullable(); // Additional settings for each method
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_method_settings');
    }
};
