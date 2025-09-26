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
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('exchange_rate', 10, 6)->nullable()->after('currency');
            $table->decimal('converted_amount', 15, 2)->nullable()->after('exchange_rate');
            $table->string('converted_currency', 3)->nullable()->after('converted_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'converted_amount', 'converted_currency']);
        });
    }
};