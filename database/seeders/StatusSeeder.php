<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            // Monetary Donation Statuses
            [
                'name' => 'pending',
                'display_name' => 'Pending',
                'type' => 'monetary',
                'color' => '#F59E0B',
                'is_fixed' => true,
                'sort_order' => 1,
                'description' => 'Monetary donation is pending review or assignment',
            ],
            [
                'name' => 'assigned',
                'display_name' => 'Assigned',
                'type' => 'monetary',
                'color' => '#3B82F6',
                'is_fixed' => true,
                'sort_order' => 2,
                'description' => 'Monetary donation has been assigned to a volunteer',
            ],
            [
                'name' => 'in_progress',
                'display_name' => 'In Progress',
                'type' => 'monetary',
                'color' => '#8B5CF6',
                'is_fixed' => true,
                'sort_order' => 3,
                'description' => 'Monetary donation is being processed or partially used',
            ],
            [
                'name' => 'completed',
                'display_name' => 'Completed',
                'type' => 'monetary',
                'color' => '#10B981',
                'is_fixed' => true,
                'sort_order' => 4,
                'description' => 'Monetary donation has been fully processed and is ready for providing',
            ],
            [
                'name' => 'cancelled',
                'display_name' => 'Cancelled',
                'type' => 'monetary',
                'color' => '#EF4444',
                'is_fixed' => true,
                'sort_order' => 5,
                'description' => 'Monetary donation has been cancelled',
            ],
            [
                'name' => 'rejected',
                'display_name' => 'Rejected',
                'type' => 'monetary',
                'color' => '#DC2626',
                'is_fixed' => true,
                'sort_order' => 6,
                'description' => 'Monetary donation has been rejected',
            ],

            // Beneficiary Statuses
            [
                'name' => 'pending',
                'display_name' => 'Pending',
                'type' => 'beneficiary',
                'color' => '#F59E0B',
                'is_fixed' => true,
                'sort_order' => 1,
                'description' => 'Request is pending review',
            ],
            [
                'name' => 'under_review',
                'display_name' => 'Under Review',
                'type' => 'beneficiary',
                'color' => '#3B82F6',
                'is_fixed' => true,
                'sort_order' => 2,
                'description' => 'Request is being reviewed',
            ],
            [
                'name' => 'approved',
                'display_name' => 'Approved',
                'type' => 'beneficiary',
                'color' => '#10B981',
                'is_fixed' => true,
                'sort_order' => 3,
                'description' => 'Request has been approved',
            ],
            [
                'name' => 'rejected',
                'display_name' => 'Rejected',
                'type' => 'beneficiary',
                'color' => '#EF4444',
                'is_fixed' => true,
                'sort_order' => 4,
                'description' => 'Request has been rejected',
            ],
            [
                'name' => 'fulfilled',
                'display_name' => 'Fulfilled',
                'type' => 'beneficiary',
                'color' => '#059669',
                'is_fixed' => true,
                'sort_order' => 5,
                'description' => 'Request has been fulfilled',
            ],

            // Materialistic Donation Statuses
            [
                'name' => 'pending',
                'display_name' => 'Pending',
                'type' => 'materialistic',
                'color' => '#F59E0B',
                'is_fixed' => true,
                'sort_order' => 1,
                'description' => 'Materialistic donation is pending',
            ],
            [
                'name' => 'available',
                'display_name' => 'Available',
                'type' => 'materialistic',
                'color' => '#10B981',
                'is_fixed' => true,
                'sort_order' => 2,
                'description' => 'Materialistic donation is available for distribution',
            ],
            [
                'name' => 'donated',
                'display_name' => 'Donated',
                'type' => 'materialistic',
                'color' => '#059669',
                'is_fixed' => true,
                'sort_order' => 3,
                'description' => 'Materialistic donation has been provided',
            ],
            [
                'name' => 'expired',
                'display_name' => 'Expired',
                'type' => 'materialistic',
                'color' => '#6B7280',
                'is_fixed' => true,
                'sort_order' => 4,
                'description' => 'Materialistic donation has expired',
            ],
            [
                'name' => 'damaged',
                'display_name' => 'Damaged',
                'type' => 'materialistic',
                'color' => '#7C2D12',
                'is_fixed' => true,
                'sort_order' => 5,
                'description' => 'Materialistic donation has been damaged',
            ],

            // Service Donation Statuses
            [
                'name' => 'pending',
                'display_name' => 'Pending',
                'type' => 'service',
                'color' => '#F59E0B',
                'is_fixed' => true,
                'sort_order' => 1,
                'description' => 'Service donation is pending',
            ],
            [
                'name' => 'available',
                'display_name' => 'Available',
                'type' => 'service',
                'color' => '#10B981',
                'is_fixed' => true,
                'sort_order' => 2,
                'description' => 'Service donation is available for provision',
            ],
            [
                'name' => 'donated',
                'display_name' => 'Donated',
                'type' => 'service',
                'color' => '#059669',
                'is_fixed' => true,
                'sort_order' => 3,
                'description' => 'Service donation has been provided',
            ],
            [
                'name' => 'expired',
                'display_name' => 'Expired',
                'type' => 'service',
                'color' => '#6B7280',
                'is_fixed' => true,
                'sort_order' => 4,
                'description' => 'Service donation has expired',
            ],
            [
                'name' => 'cancelled',
                'display_name' => 'Cancelled',
                'type' => 'service',
                'color' => '#EF4444',
                'is_fixed' => true,
                'sort_order' => 5,
                'description' => 'Service donation has been cancelled',
            ],
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                ['name' => $status['name'], 'type' => $status['type']],
                $status
            );
        }
    }
}
