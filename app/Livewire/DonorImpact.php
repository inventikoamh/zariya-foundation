<?php

namespace App\Livewire;

use App\Services\DonationHistoryService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.user')]
class DonorImpact extends Component
{
    public $donorImpact = [];
    public $totalBeneficiaries = 0;
    public $totalAmountProvided = 0;
    public $totalQuantityProvided = 0;
    public $totalServiceInstances = 0;

    public function mount()
    {
        $this->loadDonorImpact();
    }

    public function loadDonorImpact()
    {
        $donationHistoryService = new DonationHistoryService();
        $this->donorImpact = $donationHistoryService->getDonorImpact(auth()->id());

        // Calculate totals
        $this->totalBeneficiaries = $this->donorImpact->sum('total_beneficiaries');
        $this->totalAmountProvided = $this->donorImpact->sum('total_amount_provided');
        $this->totalQuantityProvided = $this->donorImpact->sum('total_quantity_provided');
        $this->totalServiceInstances = $this->donorImpact->sum('total_service_instances');
    }

    public function getTypeIcon($type)
    {
        return match($type) {
            'monetary' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
            'materialistic' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'service' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function render()
    {
        return view('livewire.donor-impact');
    }
}
