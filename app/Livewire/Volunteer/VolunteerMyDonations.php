<?php

namespace App\Livewire\Volunteer;

use App\Models\Donation;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.volunteer')]
class VolunteerMyDonations extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';

    // Cancellation modal properties
    public $showCancelModal = false;
    public $donationToCancel = null;
    public $cancellationReason = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function openCancelModal($donationId)
    {
        try {
            $donation = Donation::where('id', $donationId)
                ->where('donor_id', auth()->id())
                ->first();

            if (!$donation) {
                session()->flash('error', 'Donation not found or you do not have permission to cancel it.');
                return;
            }

            // Only allow cancellation if donation is still pending or assigned
            if (!in_array($donation->status, ['pending', 'assigned'])) {
                session()->flash('error', 'This donation cannot be cancelled as it has already been processed.');
                return;
            }

            $this->donationToCancel = $donation;
            $this->cancellationReason = '';
            $this->showCancelModal = true;


        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function cancelDonation()
    {
        $this->validate([
            'cancellationReason' => 'required|string|min:10|max:500',
        ]);

        try {
            if ($this->donationToCancel) {
                $this->donationToCancel->update([
                    'status' => 'cancelled',
                    'notes' => $this->donationToCancel->notes . "\n\nCancellation Reason: " . $this->cancellationReason,
                ]);

                session()->flash('success', 'Donation has been cancelled successfully.');
                $this->showCancelModal = false;
                $this->donationToCancel = null;
                $this->cancellationReason = '';
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while cancelling the donation: ' . $e->getMessage());
        }
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->donationToCancel = null;
        $this->cancellationReason = '';
    }

    public function render()
    {
        $query = Donation::where('donor_id', auth()->id())
            ->with(['country', 'state', 'city', 'assignedTo', 'remarks' => function($q) {
                $q->orderBy('created_at', 'desc');
            }]);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('notes', 'like', '%' . $this->search . '%')
                  ->orWhereJsonContains('details->item_name', $this->search)
                  ->orWhereJsonContains('details->service_type', $this->search)
                  ->orWhereJsonContains('details->item_description', $this->search)
                  ->orWhereJsonContains('details->service_description', $this->search);
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply type filter
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        $donations = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.donations.my-donations', [
            'donations' => $donations,
            'statusOptions' => [
                'pending' => 'Pending',
                'assigned' => 'Assigned',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],
            'typeOptions' => [
                'monetary' => 'Monetary',
                'materialistic' => 'Materialistic',
                'service' => 'Service',
            ]
        ]);
    }
}
