<?php

namespace App\Livewire\Admin\Donations;

use App\Models\Donation;
use App\Models\User;
use App\Models\Remark;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\StatusHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class DonationShow extends Component
{
    public Donation $donation;
    public $newRemark = '';
    public $remarkType = 'general';
    public $isInternal = false;

    // Form properties
    public $newStatus = '';
    public $statusRemark = '';
    public $selectedAccountId = '';
    public $exchangeRate = '';
    public $convertedAmount = '';
    public $convertedCurrency = '';
    public $selectedVolunteerId = '';
    public $assignmentNote = '';

    // Priority and urgent controls
    public $priority = '';
    public $isUrgent = false;

    public function mount(Donation $donation)
    {
        $this->donation = $donation;

        // Initialize form fields
        $this->newStatus = $donation->status;
        $this->selectedVolunteerId = $donation->assigned_to ?? '';
        $this->priority = $donation->priority ?? Donation::PRIORITY_MEDIUM;
        $this->isUrgent = $donation->is_urgent ?? false;
    }

    public function addRemark()
    {
        $this->validate([
            'newRemark' => 'required|string|max:1000',
            'remarkType' => 'required|in:status_update,assignment,progress,completion,cancellation,general',
        ]);

        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => $this->remarkType,
            'remark' => $this->newRemark,
            'is_internal' => $this->isInternal,
        ]);

        $this->newRemark = '';
        $this->remarkType = 'general';
        $this->isInternal = false;

        session()->flash('success', 'Remark added successfully.');
    }


    public function updateDonationStatus()
    {
        $statusOptions = StatusHelper::getStatusOptions($this->donation->type);
        $statusValues = implode(',', array_keys($statusOptions));

        $this->validate([
            'newStatus' => "required|in:{$statusValues}",
            'statusRemark' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $this->donation->status;

        $this->donation->update(['status' => $this->newStatus]);

        // Add status update remark
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'status_update',
            'remark' => $this->statusRemark ?: "Status changed from {$oldStatus} to {$this->newStatus}",
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $this->newStatus,
            ],
        ]);

        $this->statusRemark = '';

        session()->flash('success', 'Status updated successfully.');
    }


    public function completeMonetaryDonation()
    {
        $this->validate([
            'selectedAccountId' => 'required|exists:accounts,id',
        ]);

        $account = Account::findOrFail($this->selectedAccountId);

        // Check if currency conversion is needed
        $donationCurrency = $this->donation->details['currency'] ?? 'USD';
        $accountCurrency = $account->currency;

        if ($donationCurrency !== $accountCurrency) {
            $this->validate([
                'exchangeRate' => 'required|numeric|min:0.0001',
            ]);

            $this->convertedAmount = $this->donation->details['amount'] * $this->exchangeRate;
            $this->convertedCurrency = $accountCurrency;
        } else {
            $this->exchangeRate = null;
            $this->convertedAmount = null;
            $this->convertedCurrency = null;
        }

        // Create transaction for monetary donation
        $transaction = Transaction::create([
            'transaction_number' => 'TXN' . date('Ymd') . rand(1000, 9999),
            'type' => Transaction::TYPE_DONATION,
            'amount' => $this->donation->details['amount'],
            'currency' => $donationCurrency,
            'exchange_rate' => $this->exchangeRate,
            'converted_amount' => $this->convertedAmount,
            'converted_currency' => $this->convertedCurrency,
            'description' => "Monetary donation from {$this->donation->donor->first_name} {$this->donation->donor->last_name}",
            'status' => Transaction::STATUS_PENDING,
            'to_account_id' => $account->id,
            'donation_id' => $this->donation->id,
            'created_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'processed_at' => now(),
        ]);

        // Update account balance
        $account->update([
            'current_balance' => $account->current_balance + ($this->convertedAmount ?? $this->donation->details['amount'])
        ]);

        // Update donation status
        $this->donation->update([
            'status' => Donation::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        // Add completion remark
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'completion',
            'remark' => "Donation completed. Amount transferred to {$account->name} account.",
        ]);

        $this->selectedAccountId = '';
        $this->exchangeRate = '';
        $this->convertedAmount = '';
        $this->convertedCurrency = '';

        session()->flash('success', 'Monetary donation completed and amount transferred to account.');
    }


    public function assignToVolunteer()
    {
        $this->validate([
            'selectedVolunteerId' => 'required|exists:users,id',
            'assignmentNote' => 'nullable|string|max:1000',
        ]);

        $volunteer = User::findOrFail($this->selectedVolunteerId);

        $this->donation->update([
            'assigned_to' => $this->selectedVolunteerId,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'status' => Donation::STATUS_ASSIGNED,
        ]);

        // Add assignment remark
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'assignment',
            'remark' => $this->assignmentNote ?: "Assigned to {$volunteer->first_name} {$volunteer->last_name}",
            'metadata' => [
                'assigned_to' => $this->selectedVolunteerId,
                'assigned_by' => auth()->id(),
            ],
        ]);

        $this->assignmentNote = '';

        session()->flash('success', 'Donation assigned successfully.');
    }



    public function cancelDonation()
    {
        $this->donation->update([
            'status' => Donation::STATUS_CANCELLED,
        ]);

        // Add cancellation remark
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'cancellation',
            'remark' => 'Donation cancelled',
        ]);

        session()->flash('success', 'Donation cancelled.');
    }

    public function updatePriority()
    {
        $this->validate([
            'priority' => 'required|in:' . implode(',', [
                Donation::PRIORITY_LOW,
                Donation::PRIORITY_MEDIUM,
                Donation::PRIORITY_HIGH,
                Donation::PRIORITY_CRITICAL
            ]),
        ]);

        $oldPriority = $this->donation->priority;
        $this->donation->update(['priority' => $this->priority]);

        // Add priority update remark
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'priority_change',
            'remark' => "Priority changed from {$this->getPriorityLabel($oldPriority)} to {$this->getPriorityLabel($this->priority)}",
            'metadata' => [
                'old_priority' => $oldPriority,
                'new_priority' => $this->priority,
            ],
        ]);

        session()->flash('success', 'Priority updated successfully.');
    }

    public function updateUrgentStatus()
    {
        $oldUrgentStatus = $this->donation->is_urgent;
        $this->donation->update(['is_urgent' => $this->isUrgent]);

        // Add urgent status update remark
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'urgent_change',
            'remark' => "Urgent status changed from " . ($oldUrgentStatus ? 'Yes' : 'No') . " to " . ($this->isUrgent ? 'Yes' : 'No'),
            'metadata' => [
                'old_urgent' => $oldUrgentStatus,
                'new_urgent' => $this->isUrgent,
            ],
        ]);

        session()->flash('success', 'Urgent status updated successfully.');
    }

    private function getPriorityLabel($priority)
    {
        return match($priority) {
            Donation::PRIORITY_LOW => 'Low',
            Donation::PRIORITY_MEDIUM => 'Medium',
            Donation::PRIORITY_HIGH => 'High',
            Donation::PRIORITY_CRITICAL => 'Critical',
            default => 'Unknown',
        };
    }

    public function render()
    {
        $this->donation->load(['donor', 'assignedTo', 'assignedBy', 'country', 'state', 'city', 'remarks.user']);

        $volunteers = User::role('VOLUNTEER')
            ->whereNotNull('first_name')
            ->where('first_name', '!=', '')
            ->orderBy('first_name')
            ->get();
        $accounts = Account::active()->orderBy('name')->get();

        return view('livewire.admin.donations.donation-show', [
            'volunteers' => $volunteers,
            'accounts' => $accounts,
            'statusOptions' => StatusHelper::getStatusOptions($this->donation->type),
        ]);
    }
}
