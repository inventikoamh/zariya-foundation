<?php

namespace App\Livewire\Volunteer\Donations;

use App\Models\Donation;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\StatusHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.volunteer')]
class VolunteerDonationShow extends Component
{
    public Donation $donation;
    public $newRemark = '';
    public $remarkType = 'general';
    public $isInternal = false;

    // For status updates
    public $newStatus = '';
    public $statusRemark = '';

    // For monetary donation completion
    public $selectedAccountId = '';
    public $exchangeRate = null;
    public $convertedAmount = null;
    public $convertedCurrency = '';

    // Priority and urgent controls
    public $priority = '';
    public $isUrgent = false;

    public function mount(Donation $donation)
    {
        // Allow volunteers to view any donation, but restrict modifications to assigned donations only
        $this->donation = $donation->load([
            'donor.country',
            'donor.state',
            'donor.city',
            'country',
            'state',
            'city',
            'assignedTo',
            'remarks.user'
        ]);

        // Initialize form fields
        $this->newStatus = $donation->status;
        $this->priority = $donation->priority ?? Donation::PRIORITY_MEDIUM;
        $this->isUrgent = $donation->is_urgent ?? false;
    }

    public function getCanModifyProperty()
    {
        return $this->donation->assigned_to === auth()->id();
    }

    public function getCanViewRemarksProperty()
    {
        return $this->donation->assigned_to === auth()->id();
    }

    public function addRemark()
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only add remarks to donations assigned to you.');
            return;
        }

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
        if (!$this->canModify) {
            session()->flash('error', 'You can only update status for donations assigned to you.');
            return;
        }

        $this->validate([
            'newStatus' => 'required|in:pending,assigned,in_progress,completed,cancelled',
        ]);

        $oldStatus = $this->donation->status;

        // Handle monetary donation completion
        if ($this->donation->type === 'monetary' && $this->newStatus === 'completed') {
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

            // Process the transaction
            $processResult = $transaction->process();

            // Debug logging
            \Log::info('Transaction processing result', [
                'transaction_id' => $transaction->id,
                'process_result' => $processResult,
                'transaction_status' => $transaction->fresh()->status,
                'account_balance_after' => $account->fresh()->current_balance
            ]);
        }

        $this->donation->update([
            'status' => $this->newStatus,
            'completed_at' => $this->newStatus === 'completed' ? now() : null,
        ]);

        // Add status update remark
        $remarkText = "Status changed from {$oldStatus} to {$this->newStatus}";
        if ($this->statusRemark) {
            $remarkText .= ". " . $this->statusRemark;
        }

        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'status_update',
            'remark' => $remarkText,
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $this->newStatus,
            ],
        ]);

        session()->flash('success', 'Status updated successfully.');

        // Reset form fields
        $this->newStatus = $this->donation->status;
        $this->statusRemark = '';
        $this->selectedAccountId = '';
        $this->exchangeRate = null;
        $this->convertedAmount = null;
        $this->convertedCurrency = '';
    }

    public function updateStatus($status)
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only update status for donations assigned to you.');
            return;
        }

        $oldStatus = $this->donation->status;

        $this->donation->update(['status' => $status]);

        // Add status update remark
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'status_update',
            'remark' => "Status changed from {$oldStatus} to {$status}",
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $status,
            ],
        ]);

        session()->flash('success', 'Status updated successfully.');
    }

    public function completeDonation()
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only complete donations assigned to you.');
            return;
        }

        // For monetary donations, we need to handle account selection
        if ($this->donation->type === 'monetary') {
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

            // Process the transaction
            $processResult = $transaction->process();

            // Debug logging
            \Log::info('Transaction processing result', [
                'transaction_id' => $transaction->id,
                'process_result' => $processResult,
                'transaction_status' => $transaction->fresh()->status,
                'account_balance_after' => $account->fresh()->current_balance
            ]);
        }

        $this->donation->update([
            'status' => Donation::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        // Add completion remark
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'completion',
            'remark' => 'Donation marked as completed',
        ]);

        session()->flash('success', 'Donation marked as completed.');

        // Reset form fields
        $this->selectedAccountId = '';
        $this->exchangeRate = null;
        $this->convertedAmount = null;
        $this->convertedCurrency = '';
    }

    public function cancelDonation()
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only cancel donations assigned to you.');
            return;
        }

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

    public function updatedExchangeRate()
    {
        if ($this->exchangeRate && $this->donation->type === 'monetary') {
            $this->convertedAmount = $this->donation->details['amount'] * $this->exchangeRate;
        }
    }

    public function updatedSelectedAccountId()
    {
        if ($this->selectedAccountId && $this->donation->type === 'monetary') {
            $account = Account::find($this->selectedAccountId);
            if ($account) {
                $this->convertedCurrency = $account->currency;

                // Reset conversion fields if currencies match
                $donationCurrency = $this->donation->details['currency'] ?? 'USD';
                if ($donationCurrency === $account->currency) {
                    $this->exchangeRate = null;
                    $this->convertedAmount = null;
                }
            }
        }
    }

    public function updatePriority()
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only update priority for donations assigned to you.');
            return;
        }

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
        if (!$this->canModify) {
            session()->flash('error', 'You can only update urgent status for donations assigned to you.');
            return;
        }

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

        $accounts = Account::active()->orderBy('name')->get();

        return view('livewire.volunteer.donations.volunteer-donation-show', [
            'accounts' => $accounts,
            'statusOptions' => StatusHelper::getStatusOptions($this->donation->type),
        ]);
    }
}
