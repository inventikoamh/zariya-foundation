<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'account_number',
        'type',
        'bank_name',
        'branch_name',
        'ifsc_code',
        'currency',
        'opening_balance',
        'current_balance',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Constants for account types
    const TYPE_BANK = 'bank';
    const TYPE_CASH = 'cash';

    /**
     * Get the user who created this account
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all transactions from this account
     */
    public function fromTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    /**
     * Get all transactions to this account
     */
    public function toTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    /**
     * Get all expenses from this account
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get all donations assigned to this account
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get the type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_BANK => 'Bank Account',
            self::TYPE_CASH => 'Cash Account',
            default => 'Unknown',
        };
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalanceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->current_balance, 2);
    }

    /**
     * Update account balance with optional negative balance protection
     */
    public function updateBalance(float $amount, string $operation = 'add', bool $allowNegative = false): bool
    {
        $newBalance = $this->current_balance;

        if ($operation === 'add') {
            $newBalance += $amount;
        } else {
            $newBalance -= $amount;
        }

        // Prevent negative balance unless explicitly allowed
        if ($newBalance < 0 && !$allowNegative) {
            return false;
        }

        $this->current_balance = $newBalance;
        $this->save();
        return true;
    }

    /**
     * Check if account has sufficient balance
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->current_balance >= $amount;
    }

    /**
     * Force balance to zero if negative (use only when specifically fixing negative balances)
     * Note: This should only be used by the "Fix Negative Balances" feature
     * Normal operations should allow negative balances for expenses
     */
    public function ensureBalanceIntegrity(): void
    {
        if ($this->current_balance < 0) {
            $this->current_balance = 0;
            $this->save();
        }
    }

    /**
     * Update opening balance and recalculate current balance
     */
    public function updateOpeningBalance(float $newOpeningBalance): void
    {
        $oldOpeningBalance = $this->opening_balance;
        $difference = $newOpeningBalance - $oldOpeningBalance;

        // Update opening balance
        $this->opening_balance = $newOpeningBalance;

        // Adjust current balance by the difference
        $this->current_balance += $difference;

        $this->save();
    }

    /**
     * Recalculate current balance based on all transactions
     */
    public function recalculateBalance(): void
    {
        // Get all transactions affecting this account
        $fromTransactions = $this->fromTransactions()->where('status', 'completed')->get();
        $toTransactions = $this->toTransactions()->where('status', 'completed')->get();

        $balance = $this->opening_balance;

        \Log::info('Recalculating balance for account', [
            'account_id' => $this->id,
            'account_name' => $this->name,
            'opening_balance' => $balance,
            'from_transactions_count' => $fromTransactions->count(),
            'to_transactions_count' => $toTransactions->count()
        ]);

        // Add income and transfers to this account
        foreach ($toTransactions as $transaction) {
            if ($transaction->type === 'income' || $transaction->type === 'donation') {
                $amount = $this->getConvertedAmountForTransaction($transaction, 'to');
                $balance += $amount;
                \Log::info('Added to balance', [
                    'transaction_id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $amount,
                    'new_balance' => $balance
                ]);
            } elseif ($transaction->type === 'transfer') {
                $amount = $this->getConvertedAmountForTransaction($transaction, 'to');
                $balance += $amount;
                \Log::info('Added transfer to balance', [
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                    'new_balance' => $balance
                ]);
            }
        }

        // Subtract expenses and transfers from this account
        foreach ($fromTransactions as $transaction) {
            if ($transaction->type === 'expense') {
                $amount = $this->getConvertedAmountForTransaction($transaction, 'from');
                $balance -= $amount;
                \Log::info('Subtracted expense from balance', [
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                    'new_balance' => $balance
                ]);
            } elseif ($transaction->type === 'transfer') {
                $amount = $this->getConvertedAmountForTransaction($transaction, 'from');
                $balance -= $amount;
                \Log::info('Subtracted transfer from balance', [
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                    'new_balance' => $balance
                ]);
            }
        }

        \Log::info('Final calculated balance', [
            'account_id' => $this->id,
            'calculated_balance' => $balance,
            'old_balance' => $this->current_balance
        ]);

        $this->current_balance = $balance; // Allow negative balances (expenses can cause this)
        $this->save();
    }

    /**
     * Get the correct amount for a transaction considering currency conversion
     */
    private function getConvertedAmountForTransaction(Transaction $transaction, string $direction): float
    {
        if ($direction === 'to' && $transaction->to_account_id === $this->id) {
            // For incoming transactions, use converted amount if available
            if ($transaction->converted_currency === $this->currency && $transaction->converted_amount) {
                return $transaction->converted_amount;
            }
            return $transaction->amount;
        } elseif ($direction === 'from' && $transaction->from_account_id === $this->id) {
            // For outgoing transactions, use original amount
            return $transaction->amount;
        }

        return 0;
    }

    /**
     * Scope for active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific currency
     */
    public function scopeWithCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }
}
