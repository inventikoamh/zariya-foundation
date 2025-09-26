<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove DONOR and BENEFICIARY roles
        Role::whereIn('name', ['DONOR', 'BENEFICIARY'])->delete();
        
        // Update existing users with DONOR or BENEFICIARY roles to have no role (normal users)
        // This will be handled in the seeder
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the roles
        Role::firstOrCreate(['name' => 'DONOR']);
        Role::firstOrCreate(['name' => 'BENEFICIARY']);
    }
};
