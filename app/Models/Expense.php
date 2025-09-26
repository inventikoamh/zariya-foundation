<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'currency',
        'exchange_rate',
        'converted_amount',
        'converted_currency',
        'category',
        'status',
        'account_id',
        'requested_by',
        'approved_by',
        'approved_at',
        'paid_at',
        'rejection_reason',
        'attachments',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'converted_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'attachments' => 'array',
    ];

    // Constants for expense categories
    const CATEGORY_OFFICE_SUPPLIES = 'office_supplies';
    const CATEGORY_UTILITIES = 'utilities';
    const CATEGORY_TRANSPORTATION = 'transportation';
    const CATEGORY_COMMUNICATION = 'communication';
    const CATEGORY_EQUIPMENT = 'equipment';
    const CATEGORY_MAINTENANCE = 'maintenance';
    const CATEGORY_TRAINING = 'training';
    const CATEGORY_EVENTS = 'events';
    const CATEGORY_MARKETING = 'marketing';
    const CATEGORY_LEGAL = 'legal';
    const CATEGORY_INSURANCE = 'insurance';
    const CATEGORY_RENT = 'rent';
    const CATEGORY_SALARIES = 'salaries';
    const CATEGORY_OTHER = 'other';

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAID = 'paid';

    /**
     * Get the account for this expense
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the user who requested this expense
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who approved this expense
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the transaction for this expense
     */
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'expense_id');
    }

    /**
     * Get the category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            self::CATEGORY_OFFICE_SUPPLIES => 'Office Supplies',
            self::CATEGORY_UTILITIES => 'Utilities',
            self::CATEGORY_TRANSPORTATION => 'Transportation',
            self::CATEGORY_COMMUNICATION => 'Communication',
            self::CATEGORY_EQUIPMENT => 'Equipment',
            self::CATEGORY_MAINTENANCE => 'Maintenance',
            self::CATEGORY_TRAINING => 'Training',
            self::CATEGORY_EVENTS => 'Events',
            self::CATEGORY_MARKETING => 'Marketing',
            self::CATEGORY_LEGAL => 'Legal',
            self::CATEGORY_INSURANCE => 'Insurance',
            self::CATEGORY_RENT => 'Rent',
            self::CATEGORY_SALARIES => 'Salaries',
            self::CATEGORY_OTHER => 'Other',
            default => 'Unknown',
        };
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PAID => 'Paid',
            default => 'Unknown',
        };
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get formatted exchange rate information
     */
    public function getFormattedExchangeRateAttribute(): string
    {
        if ($this->exchange_rate && $this->converted_amount && $this->converted_currency) {
            return $this->currency . ' ' . number_format($this->amount, 2) . ' → ' . $this->converted_currency . ' ' . number_format($this->converted_amount, 2) . ' (Rate: ' . number_format($this->exchange_rate, 6) . ')';
        }
        return $this->formatted_amount;
    }

    /**
     * Check if expense has currency conversion
     */
    public function hasCurrencyConversion(): bool
    {
        return !empty($this->exchange_rate) && !empty($this->converted_amount) && !empty($this->converted_currency);
    }

    /**
     * Check if expense is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if expense is paid
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if expense is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by category
     */
    public function scopeWithCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for pending expenses
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved expenses
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for expenses by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('requested_by', $userId);
    }

    /**
     * Delete expense with proper transaction cleanup and balance restoration
     */
    public function deleteWithTransactionCleanup(): bool
    {
        // Only process if expense is approved or paid (has associated transaction)
        if (!in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PAID])) {
            return $this->forceDelete();
        }

        $transaction = $this->transaction;
        if (!$transaction) {
            return $this->forceDelete();
        }

        // Get the account to restore balance
        $account = $this->account;
        if (!$account) {
            return $this->forceDelete();
        }

        // Calculate the amount to restore to the account
        $amountToRestore = $this->getAmountToRestore($account);

        // Restore the balance to the account using model method
        $account->updateBalance($amountToRestore, 'add');

        // Delete the expense (transaction will be automatically deleted due to cascade foreign key)
        return $this->forceDelete();
    }

    /**
     * Update expense and reset to pending status if it was approved/paid
     */
    public function updateWithApprovalReset(array $data): bool
    {
        // If expense was approved/paid, we need to restore balance and delete transaction
        if (in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PAID])) {
            $transaction = $this->transaction;
            $account = $this->account;

            if ($transaction && $account) {
                // Calculate the amount to restore
                $amountToRestore = $this->getAmountToRestore($account);

                // Restore the balance to the account using model method
                $account->updateBalance($amountToRestore, 'add');

                // Delete the transaction (cascade will handle this, but let's be explicit)
                $transaction->forceDelete();
            }

            // Reset approval fields and set status to pending
            $data['status'] = self::STATUS_PENDING;
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['paid_at'] = null;
        }

        // Update the expense with new data
        return $this->update($data);
    }

    /**
     * Get the amount to restore to the account based on currency conversion
     */
    private function getAmountToRestore(Account $account): float
    {
        // If currencies match, restore the original amount
        if ($this->currency === $account->currency) {
            return $this->amount;
        }

        // If there was currency conversion, restore the converted amount
        if ($this->converted_amount && $this->converted_currency === $account->currency) {
            return $this->converted_amount;
        }

        // Fallback to original amount (should not happen with proper validation)
        return $this->amount;
    }
}
