<?php

namespace App\Services;

use App\Models\DonationHistory;
use App\Models\Donation;
use App\Models\Beneficiary;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DonationHistoryService
{
    /**
     * Provide a monetary donation to a beneficiary
     */
    public function provideMonetaryDonation(
        int $beneficiaryId,
        int $donationId,
        float $amount,
        string $currency,
        ?float $exchangeRate = null,
        ?float $convertedAmount = null,
        ?string $convertedCurrency = null,
        ?int $accountId = null,
        ?string $notes = null
    ): DonationHistory {
        return DB::transaction(function () use (
            $beneficiaryId,
            $donationId,
            $amount,
            $currency,
            $exchangeRate,
            $convertedAmount,
            $convertedCurrency,
            $accountId,
            $notes
        ) {
            // Create donation history record
            $donationHistory = DonationHistory::create([
                'beneficiary_id' => $beneficiaryId,
                'donation_id' => $donationId,
                'provided_by' => Auth::id(),
                'donation_type' => 'monetary',
                'amount' => $amount,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'converted_amount' => $convertedAmount,
                'converted_currency' => $convertedCurrency,
                'account_id' => $accountId,
                'status' => 'pending',
                'notes' => $notes,
                'provided_at' => now(),
            ]);

            return $donationHistory;
        });
    }

    /**
     * Provide a materialistic donation to a beneficiary
     */
    public function provideMaterialisticDonation(
        int $beneficiaryId,
        int $donationId,
        int $quantity,
        string $unit,
        ?string $description = null,
        ?string $notes = null
    ): DonationHistory {
        return DB::transaction(function () use (
            $beneficiaryId,
            $donationId,
            $quantity,
            $unit,
            $description,
            $notes
        ) {
            // Create donation history record
            $donationHistory = DonationHistory::create([
                'beneficiary_id' => $beneficiaryId,
                'donation_id' => $donationId,
                'provided_by' => Auth::id(),
                'donation_type' => 'materialistic',
                'quantity' => $quantity,
                'unit' => $unit,
                'description' => $description,
                'status' => 'pending',
                'notes' => $notes,
                'provided_at' => now(),
            ]);

            return $donationHistory;
        });
    }

    /**
     * Provide a service donation to a beneficiary
     */
    public function provideServiceDonation(
        int $beneficiaryId,
        int $donationId,
        string $description,
        ?string $notes = null
    ): DonationHistory {
        return DB::transaction(function () use (
            $beneficiaryId,
            $donationId,
            $description,
            $notes
        ) {
            // Create donation history record
            $donationHistory = DonationHistory::create([
                'beneficiary_id' => $beneficiaryId,
                'donation_id' => $donationId,
                'provided_by' => Auth::id(),
                'donation_type' => 'service',
                'description' => $description,
                'status' => 'pending',
                'notes' => $notes,
                'provided_at' => now(),
            ]);

            return $donationHistory;
        });
    }

    /**
     * Approve a donation history record
     */
    public function approveDonationHistory(int $donationHistoryId): DonationHistory
    {
        return DB::transaction(function () use ($donationHistoryId) {
            $donationHistory = DonationHistory::findOrFail($donationHistoryId);

            // Update status
            $donationHistory->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // If it's a monetary donation, create transaction and deduct from account
            if ($donationHistory->donation_type === 'monetary' && $donationHistory->account_id) {
                $this->processMonetaryDonation($donationHistory);
            }

            // Update donation status if needed
            $this->updateDonationStatus($donationHistory);

            return $donationHistory->fresh();
        });
    }

    /**
     * Process monetary donation (create transaction and deduct from account)
     */
    private function processMonetaryDonation(DonationHistory $donationHistory): void
    {
        $account = Account::findOrFail($donationHistory->account_id);
        $amount = $donationHistory->converted_amount ?? $donationHistory->amount;
        $currency = $donationHistory->converted_currency ?? $donationHistory->currency;

        // Check if account has sufficient balance
        if ($account->balance < $amount) {
            throw new \Exception('Insufficient account balance');
        }

        // Deduct from account
        $account->decrement('balance', $amount);

        // Create transaction record
        Transaction::create([
            'account_id' => $account->id,
            'donation_id' => $donationHistory->donation_id,
            'type' => 'debit',
            'amount' => $amount,
            'currency' => $currency,
            'description' => "Donation provided to beneficiary #{$donationHistory->beneficiary_id}",
            'reference' => "DONATION_HISTORY_{$donationHistory->id}",
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Update donation status based on donation history
     */
    private function updateDonationStatus(DonationHistory $donationHistory): void
    {
        $donation = $donationHistory->donation;

        // Check if donation should be marked as partially donated or fully donated
        $totalProvided = $donation->donationHistories()->approved()->sum(function ($history) {
            if ($history->donation_type === 'monetary') {
                return $history->converted_amount ?? $history->amount;
            } elseif ($history->donation_type === 'materialistic') {
                return $history->quantity;
            } else {
                return 1; // For services, count each instance
            }
        });

        $originalAmount = $donation->amount ?? $donation->details['quantity'] ?? 1;

        if ($totalProvided >= $originalAmount) {
            $donation->update(['status' => 'completed']);
        } elseif ($totalProvided > 0) {
            // Mark as partially donated if not already
            if ($donation->status !== 'completed') {
                $donation->update(['status' => 'in_progress']);
            }
        }
    }

    /**
     * Get donation history for a specific beneficiary
     */
    public function getBeneficiaryDonationHistory(int $beneficiaryId)
    {
        return DonationHistory::with(['donation', 'providedBy', 'account'])
            ->where('beneficiary_id', $beneficiaryId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get donation history for a specific donation
     */
    public function getDonationHistory(int $donationId)
    {
        return DonationHistory::with(['beneficiary', 'providedBy', 'account'])
            ->where('donation_id', $donationId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get donor's donation impact (for donor view)
     */
    public function getDonorImpact(int $donorId)
    {
        return Donation::with(['donationHistories.beneficiary'])
            ->where('donor_id', $donorId)
            ->whereHas('donationHistories')
            ->get()
            ->map(function ($donation) {
                return [
                    'donation' => $donation,
                    'total_beneficiaries' => $donation->total_beneficiaries_helped,
                    'total_amount_provided' => $donation->total_amount_provided,
                    'total_quantity_provided' => $donation->total_quantity_provided,
                    'total_service_instances' => $donation->total_service_instances,
                    'beneficiaries_helped' => $donation->donationHistories->map(function ($history) {
                        return [
                            'beneficiary_id' => $history->beneficiary_id,
                            'donation_type' => $history->donation_type,
                            'amount' => $history->amount,
                            'quantity' => $history->quantity,
                            'description' => $history->description,
                            'provided_at' => $history->provided_at,
                            'status' => $history->status,
                        ];
                    }),
                ];
            });
    }

    /**
     * Cancel a donation history record
     */
    public function cancelDonationHistory(int $donationHistoryId, string $reason = null): DonationHistory
    {
        return DB::transaction(function () use ($donationHistoryId, $reason) {
            $donationHistory = DonationHistory::findOrFail($donationHistoryId);

            // If it was already approved and it's monetary, reverse the transaction
            if ($donationHistory->status === 'approved' && $donationHistory->donation_type === 'monetary') {
                $this->reverseMonetaryDonation($donationHistory);
            }

            // Update status
            $donationHistory->update([
                'status' => 'cancelled',
                'notes' => $donationHistory->notes . "\nCancelled: " . $reason,
            ]);

            return $donationHistory->fresh();
        });
    }

    /**
     * Reverse monetary donation (refund to account)
     */
    private function reverseMonetaryDonation(DonationHistory $donationHistory): void
    {
        $account = Account::findOrFail($donationHistory->account_id);
        $amount = $donationHistory->converted_amount ?? $donationHistory->amount;
        $currency = $donationHistory->converted_currency ?? $donationHistory->currency;

        // Refund to account
        $account->increment('balance', $amount);

        // Create reverse transaction record
        Transaction::create([
            'account_id' => $account->id,
            'donation_id' => $donationHistory->donation_id,
            'type' => 'credit',
            'amount' => $amount,
            'currency' => $currency,
            'description' => "Refund for cancelled donation to beneficiary #{$donationHistory->beneficiary_id}",
            'reference' => "REFUND_DONATION_HISTORY_{$donationHistory->id}",
            'created_by' => Auth::id(),
        ]);
    }
}
