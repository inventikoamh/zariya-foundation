<?php

namespace App\Livewire\Volunteer;

use App\Models\Donation;
use App\Models\Remark;
use App\Models\Account;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.volunteer')]
class VolunteerDonationDetails extends Component
{
    public Donation $donation;

    // Cancellation modal properties
    public $showCancelModal = false;
    public $cancellationReason = '';

    // Status update modal properties
    public $showStatusModal = false;
    public $newStatus = '';
    public $statusRemark = '';
    public $selectedAccountId = '';
    public $exchangeRate = null;
    public $convertedAmount = null;
    public $convertedCurrency = null;

    public function updatedExchangeRate()
    {
        if ($this->exchangeRate && $this->donation && $this->donation->amount) {
            $this->convertedAmount = number_format($this->donation->amount * $this->exchangeRate, 2, '.', '');
        }
    }

    public function mount(Donation $donation)
    {
        $user = auth()->user();

        // Check if user is admin, volunteer, or the donor of this donation
        $isAdmin = $user->hasRole('SUPER_ADMIN');
        $isVolunteer = $user->hasRole('VOLUNTEER');
        $isDonor = $donation->donor_id === $user->id;

        if (!$isAdmin && !$isVolunteer && !$isDonor) {
            abort(403, 'You are not authorized to view this donation.');
        }

        $this->donation = $donation->load(['country', 'state', 'city', 'assignedTo', 'remarks.user']);
    }

    public function openCancelModal()
    {
        $user = auth()->user();

        // Check if user can cancel this donation
        $canCancel = $user->hasRole('SUPER_ADMIN') ||
                    ($user->hasRole('VOLUNTEER') && $this->donation->assigned_to === $user->id) ||
                    ($this->donation->donor_id === $user->id && in_array($this->donation->status, ['pending', 'assigned']));

        if (!$canCancel) {
            session()->flash('error', 'You are not authorized to cancel this donation.');
            return;
        }

        $this->cancellationReason = '';
        $this->showCancelModal = true;
    }

    public function cancelDonation()
    {
        $this->validate([
            'cancellationReason' => 'required|string|min:10|max:500',
        ]);

        try {
            $this->donation->update([
                'status' => 'cancelled',
                'notes' => $this->donation->notes . "\n\nCancellation Reason: " . $this->cancellationReason,
            ]);

            // Add a remark about the cancellation
            Remark::create([
                'donation_id' => $this->donation->id,
                'user_id' => auth()->id(),
                'type' => 'cancellation',
                'remark' => 'Donation cancelled: ' . $this->cancellationReason,
                'is_internal' => false,
            ]);

            session()->flash('success', 'Donation has been cancelled successfully.');
            $this->showCancelModal = false;
            $this->cancellationReason = '';

            // Refresh the donation data
            $this->donation->refresh();
            $this->donation->load(['country', 'state', 'city', 'assignedTo', 'remarks.user']);

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while cancelling the donation: ' . $e->getMessage());
        }
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancellationReason = '';
    }

    public function openStatusModal()
    {
        $user = auth()->user();

        // Check if user can update status
        $canUpdateStatus = $user->hasRole('SUPER_ADMIN') ||
                          ($user->hasRole('VOLUNTEER') && $this->donation->assigned_to === $user->id);

        if (!$canUpdateStatus) {
            session()->flash('error', 'You are not authorized to update the status of this donation.');
            return;
        }

        $this->newStatus = $this->donation->status;
        $this->statusRemark = '';
        $this->selectedAccountId = '';
        $this->exchangeRate = null;
        $this->convertedAmount = null;
        $this->convertedCurrency = null;
        $this->showStatusModal = true;
    }

    public function updateStatus()
    {
        $this->validate([
            'newStatus' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            'statusRemark' => 'required|string|min:5|max:500',
        ]);

        // Additional validation for monetary donations
        if ($this->donation->type === 'monetary' && $this->newStatus === 'completed') {
            $this->validate([
                'selectedAccountId' => 'required|exists:accounts,id',
            ]);
        }

        try {
            $oldStatus = $this->donation->status;
            $this->donation->update([
                'status' => $this->newStatus,
            ]);

            // Add a remark about the status change
            Remark::create([
                'donation_id' => $this->donation->id,
                'user_id' => auth()->id(),
                'type' => 'status_update',
                'remark' => "Status changed from {$oldStatus} to {$this->newStatus}. " . $this->statusRemark,
                'is_internal' => false,
            ]);

            // Handle monetary completion
            if ($this->donation->type === 'monetary' && $this->newStatus === 'completed') {
                $account = Account::find($this->selectedAccountId);

                // Create transaction record
                Transaction::create([
                    'donation_id' => $this->donation->id,
                    'account_id' => $this->selectedAccountId,
                    'amount' => $this->donation->amount,
                    'currency' => $this->donation->currency,
                    'exchange_rate' => $this->exchangeRate,
                    'converted_amount' => $this->convertedAmount,
                    'converted_currency' => $this->convertedCurrency,
                    'type' => 'credit',
                    'description' => 'Donation completion - ' . $this->statusRemark,
                ]);

                // Update account balance
                $account->increment('balance', $this->convertedAmount ?? $this->donation->amount);
            }

            session()->flash('success', 'Donation status updated successfully.');
            $this->showStatusModal = false;
            $this->newStatus = '';
            $this->statusRemark = '';
            $this->selectedAccountId = '';
            $this->exchangeRate = null;
            $this->convertedAmount = null;
            $this->convertedCurrency = null;

            // Refresh the donation data
            $this->donation->refresh();
            $this->donation->load(['country', 'state', 'city', 'assignedTo', 'remarks.user']);

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the donation status: ' . $e->getMessage());
        }
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->newStatus = '';
        $this->statusRemark = '';
        $this->selectedAccountId = '';
        $this->exchangeRate = null;
        $this->convertedAmount = null;
        $this->convertedCurrency = null;
    }

    public function render()
    {
        $accounts = Account::where('is_active', true)->get();

        return view('livewire.donations.donation-details', [
            'accounts' => $accounts,
        ]);
    }
}
