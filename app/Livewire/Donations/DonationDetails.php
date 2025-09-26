<?php

namespace App\Livewire\Donations;

use App\Models\Donation;
use App\Models\Remark;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\StatusHelper;
use App\Services\EnhancedAchievementService;
use Livewire\Component;
use Livewire\Attributes\Layout;

class DonationDetails extends Component
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

        // Only the donor can cancel their own donation
        if ($this->donation->donor_id !== $user->id) {
            session()->flash('error', 'You can only cancel your own donations.');
            return;
        }

        // Only allow cancellation if donation is still pending or assigned
        if (!in_array($this->donation->status, ['pending', 'assigned'])) {
            session()->flash('error', 'This donation cannot be cancelled as it has already been processed.');
            return;
        }

        $this->cancellationReason = '';
        $this->showCancelModal = true;
    }

    public function cancelDonation()
    {
        $user = auth()->user();

        // Only the donor can cancel their own donation
        if ($this->donation->donor_id !== $user->id) {
            session()->flash('error', 'You can only cancel your own donations.');
            return;
        }

        $this->validate([
            'cancellationReason' => 'required|string|min:10|max:500'
        ], [
            'cancellationReason.required' => 'Please provide a reason for cancellation.',
            'cancellationReason.min' => 'Cancellation reason must be at least 10 characters.',
            'cancellationReason.max' => 'Cancellation reason cannot exceed 500 characters.'
        ]);

        // Update donation status to cancelled
        $this->donation->update([
            'status' => 'cancelled'
        ]);

        // Add cancellation remark with user's reason
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'cancellation',
            'remark' => 'Donation cancelled by donor. Reason: ' . $this->cancellationReason,
        ]);

        $this->showCancelModal = false;
        $this->cancellationReason = '';

        session()->flash('success', 'Donation has been cancelled successfully.');
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancellationReason = '';
    }

    public function showStatusUpdateModal()
    {
        $user = auth()->user();

        // Only admins and volunteers can update donation status
        if (!$user->hasRole('SUPER_ADMIN') && !$user->hasRole('VOLUNTEER')) {
            session()->flash('error', 'You are not authorized to update donation status.');
            return;
        }

        $this->newStatus = $this->donation->status;
        $this->statusRemark = '';
        $this->selectedAccountId = $this->donation->account_id ?? '';
        $this->exchangeRate = null;
        $this->convertedAmount = null;
        $this->convertedCurrency = null;
        $this->showStatusModal = true;
    }

    public function updateDonationStatus()
    {
        $user = auth()->user();

        // Only admins and volunteers can update donation status
        if (!$user->hasRole('SUPER_ADMIN') && !$user->hasRole('VOLUNTEER')) {
            session()->flash('error', 'You are not authorized to update donation status.');
            return;
        }

        $validationRules = [
            'newStatus' => 'required|in:pending,assigned,in_progress,completed,cancelled,rejected',
            'statusRemark' => 'required|string|min:10|max:500'
        ];

        $validationMessages = [
            'newStatus.required' => 'Please select a status.',
            'statusRemark.required' => 'Please provide a remark for this status change.',
            'statusRemark.min' => 'Remark must be at least 10 characters.',
            'statusRemark.max' => 'Remark cannot exceed 500 characters.'
        ];

        // If completing a monetary donation, require account selection
        if ($this->newStatus === 'completed' && $this->donation && $this->donation->type === 'monetary') {
            $validationRules['selectedAccountId'] = 'required|exists:accounts,id';
            $validationMessages['selectedAccountId.required'] = 'Please select an account for the monetary donation.';

            // Check if currency conversion is needed
            $account = Account::find($this->selectedAccountId);
            if ($account && $account->currency !== $this->donation->currency) {
                $validationRules['exchangeRate'] = 'required|numeric|min:0.000001';
                $validationRules['convertedAmount'] = 'required|numeric|min:0.01';
                $validationMessages['exchangeRate.required'] = 'Exchange rate is required when currencies differ.';
                $validationMessages['convertedAmount.required'] = 'Converted amount is required when currencies differ.';

                // Auto-set the converted currency to the account currency
                $this->convertedCurrency = $account->currency;
            }
        }

        $this->validate($validationRules, $validationMessages);

        $oldStatus = $this->donation->status;

        // Update donation status and account if monetary
        $updateData = [
            'status' => $this->newStatus,
            'completed_at' => $this->newStatus === 'completed' ? now() : null,
        ];

        if ($this->newStatus === 'completed' && $this->donation->type === 'monetary' && $this->selectedAccountId) {
            $updateData['account_id'] = $this->selectedAccountId;
        }

        $this->donation->update($updateData);

        // Create transaction for completed monetary donations
        if ($this->newStatus === 'completed' && $this->donation->type === 'monetary' && $this->selectedAccountId) {
            $account = Account::find($this->selectedAccountId);
            $amount = $this->donation->amount;
            $currency = $this->donation->currency;

            if ($account && $amount) {
                $transactionData = [
                    'transaction_number' => 'TXN' . date('Ymd') . rand(1000, 9999),
                    'type' => Transaction::TYPE_DONATION,
                    'amount' => $amount,
                    'currency' => $currency,
                    'description' => 'Donation: ' . $this->donation->donor->name,
                    'to_account_id' => $this->selectedAccountId,
                    'donation_id' => $this->donation->id,
                    'created_by' => auth()->id(),
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'status' => Transaction::STATUS_PENDING,
                ];

                // Add exchange rate data if available (convert empty strings to null)
                if (!empty($this->exchangeRate) && !empty($this->convertedAmount) && !empty($this->convertedCurrency)) {
                    $transactionData['exchange_rate'] = $this->exchangeRate;
                    $transactionData['converted_amount'] = $this->convertedAmount;
                    $transactionData['converted_currency'] = $this->convertedCurrency;
                } else {
                    // Ensure null values for decimal columns
                    $transactionData['exchange_rate'] = null;
                    $transactionData['converted_amount'] = null;
                    $transactionData['converted_currency'] = null;
                }

                $transaction = Transaction::create($transactionData);

                // Process the transaction
                $transaction->process();
            }
        }

        // Add status update remark
        $remarkContent = "Status changed from '{$oldStatus}' to '{$this->newStatus}'. {$this->statusRemark}";
        if ($this->newStatus === 'completed' && $this->donation->type === 'monetary' && $this->selectedAccountId) {
            $account = Account::find($this->selectedAccountId);
            $remarkContent .= " Donation transferred to account: {$account->name}.";
        }

        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'general',
            'remark' => $remarkContent,
        ]);

        $this->showStatusModal = false;
        $this->newStatus = '';
        $this->statusRemark = '';
        $this->selectedAccountId = '';
        $this->exchangeRate = null;
        $this->convertedAmount = null;
        $this->convertedCurrency = null;

        // Check for achievements if donation was completed
        if ($this->newStatus === 'completed') {
            $achievementService = new EnhancedAchievementService();
            $awardedAchievements = $achievementService->checkAndAwardAchievements(
                $this->donation->donor,
                'donation_completed',
                [
                    'amount' => $this->donation->amount,
                    'type' => $this->donation->type,
                    'currency' => $this->donation->currency ?? 'INR',
                    'donation_id' => $this->donation->id
                ]
            );

            // Show achievement notifications
            if (count($awardedAchievements) > 0) {
                $achievementNames = collect($awardedAchievements)->pluck('achievement.name')->join(', ');
                session()->flash('achievements', "🎉 Congratulations! You earned: {$achievementNames}");
            }
        }

        // Refresh the donation data
        $this->donation = $this->donation->fresh(['country', 'state', 'city', 'assignedTo', 'remarks.user']);

        session()->flash('success', 'Donation status updated successfully.');
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
        return view('livewire.donations.donation-details', [
            'accounts' => Account::active()->get(),
            'statusOptions' => StatusHelper::getStatusOptions($this->donation->type)
        ]);
    }

    public function layout()
    {
        if (auth()->user()->hasRole('SUPER_ADMIN')) {
            return 'layouts.admin';
        } elseif (auth()->user()->hasRole('VOLUNTEER')) {
            return 'layouts.volunteer';
        } else {
            return 'layouts.user';
        }
    }
}
