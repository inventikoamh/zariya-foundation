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
        Schema::table('users', function (Blueprint $table) {
            // Add phone-related columns
            if (!Schema::hasColumn('users', 'phone_country_code')) {
                $table->string('phone_country_code')->default('+91');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 10)->unique()->nullable();
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable();
            }
            
            // Add user status and profile columns
            if (!Schema::hasColumn('users', 'is_disabled')) {
                $table->boolean('is_disabled')->default(false);
            }
            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable();
            }
            
            // Add location columns
            if (!Schema::hasColumn('users', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'state_id')) {
                $table->unsignedBigInteger('state_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'pincode')) {
                $table->string('pincode', 6)->nullable();
            }
            
            // Add additional profile fields
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
            }
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable();
            }
            if (!Schema::hasColumn('users', 'address_line')) {
                $table->text('address_line')->nullable();
            }
        });
        
        // Add index on phone column
        Schema::table('users', function (Blueprint $table) {
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropColumn([
                'phone_country_code',
                'phone',
                'phone_verified_at',
                'is_disabled',
                'avatar_url',
                'country_id',
                'state_id',
                'city_id',
                'pincode',
                'first_name',
                'last_name',
                'gender',
                'dob',
                'address_line'
            ]);
        });
    }
};
