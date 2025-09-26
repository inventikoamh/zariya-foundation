<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_number',
        'type',
        'amount',
        'currency',
        'exchange_rate',
        'converted_amount',
        'converted_currency',
        'description',
        'status',
        'from_account_id',
        'to_account_id',
        'donation_id',
        'expense_id',
        'created_by',
        'approved_by',
        'approved_at',
        'processed_at',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'converted_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Constants for transaction types
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_DONATION = 'donation';

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = 'TXN' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the from account
     */
    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    /**
     * Get the to account
     */
    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    /**
     * Get the donation related to this transaction
     */
    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    /**
     * Get the expense related to this transaction
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Get the user who created this transaction
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this transaction
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_INCOME => 'Income',
            self::TYPE_EXPENSE => 'Expense',
            self::TYPE_TRANSFER => 'Transfer',
            self::TYPE_DONATION => 'Donation',
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
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
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
     * Check if transaction has currency conversion
     */
    public function hasCurrencyConversion(): bool
    {
        return !empty($this->exchange_rate) && !empty($this->converted_amount) && !empty($this->converted_currency);
    }

    /**
     * Check if transaction is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transaction is failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if transaction is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Process the transaction
     */
    public function process(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        try {
            $success = true;

            // Update account balances based on transaction type
            if ($this->type === self::TYPE_INCOME && $this->to_account_id) {
                $amount = $this->getConvertedAmountForAccount($this->toAccount);
                $success = $this->toAccount->updateBalance($amount, 'add');
            } elseif ($this->type === self::TYPE_EXPENSE && $this->from_account_id) {
                $amount = $this->getConvertedAmountForAccount($this->fromAccount);

                // Allow expenses to go negative (overdraft/emergency expenses)
                $success = $this->fromAccount->updateBalance($amount, 'subtract', true);
            } elseif ($this->type === self::TYPE_TRANSFER && $this->from_account_id && $this->to_account_id) {
                $fromAmount = $this->getConvertedAmountForAccount($this->fromAccount);
                $toAmount = $this->getConvertedAmountForAccount($this->toAccount);

                // Check if source account has sufficient balance
                if (!$this->fromAccount->hasSufficientBalance($fromAmount)) {
                    $this->update([
                        'status' => self::STATUS_FAILED,
                        'notes' => $this->notes . "\nError: Insufficient balance in source account",
                    ]);
                    return false;
                }

                $success = $this->fromAccount->updateBalance($fromAmount, 'subtract');
                if ($success) {
                    $success = $this->toAccount->updateBalance($toAmount, 'add');
                }
            } elseif ($this->type === self::TYPE_DONATION && $this->to_account_id) {
                $amount = $this->getConvertedAmountForAccount($this->toAccount);
                \Log::info('Processing donation transaction', [
                    'transaction_id' => $this->id,
                    'amount' => $amount,
                    'account_id' => $this->to_account_id,
                    'account_balance_before' => $this->toAccount->current_balance
                ]);
                $success = $this->toAccount->updateBalance($amount, 'add');
                \Log::info('Donation transaction result', [
                    'transaction_id' => $this->id,
                    'success' => $success,
                    'account_balance_after' => $this->toAccount->fresh()->current_balance
                ]);
            }

            if ($success) {
                $this->update([
                    'status' => self::STATUS_COMPLETED,
                    'processed_at' => now(),
                ]);
                return true;
            } else {
                $this->update([
                    'status' => self::STATUS_FAILED,
                    'notes' => $this->notes . "\nError: Insufficient balance",
                ]);
                return false;
            }
        } catch (\Exception $e) {
            $this->update([
                'status' => self::STATUS_FAILED,
                'notes' => $this->notes . "\nError: " . $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get the converted amount for a specific account
     */
    private function getConvertedAmountForAccount($account): float
    {
        // If currencies match, return original amount
        if ($this->currency === $account->currency) {
            return $this->amount;
        }

        // If we have conversion data, use it
        if ($this->exchange_rate && $this->converted_amount && $this->converted_currency === $account->currency) {
            return $this->converted_amount;
        }

        // Fallback to original amount (should not happen with proper validation)
        return $this->amount;
    }

    /**
     * Scope for filtering by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for transactions by account
     */
    public function scopeByAccount($query, int $accountId)
    {
        return $query->where(function ($q) use ($accountId) {
            $q->where('from_account_id', $accountId)
              ->orWhere('to_account_id', $accountId);
        });
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Delete transaction with proper balance restoration
     */
    public function deleteWithBalanceRestoration(): bool
    {
        // Only process if transaction is completed (has affected balances)
        if ($this->status !== self::STATUS_COMPLETED) {
            return $this->delete();
        }

        try {
            // Restore balances based on transaction type
            if ($this->type === self::TYPE_INCOME && $this->to_account_id) {
                $amount = $this->getConvertedAmountForAccount($this->toAccount);
                $this->toAccount->updateBalance($amount, 'subtract');
            } elseif ($this->type === self::TYPE_EXPENSE && $this->from_account_id) {
                $amount = $this->getConvertedAmountForAccount($this->fromAccount);
                $this->fromAccount->updateBalance($amount, 'add');
            } elseif ($this->type === self::TYPE_TRANSFER && $this->from_account_id && $this->to_account_id) {
                $fromAmount = $this->getConvertedAmountForAccount($this->fromAccount);
                $toAmount = $this->getConvertedAmountForAccount($this->toAccount);
                $this->fromAccount->updateBalance($fromAmount, 'add');
                $this->toAccount->updateBalance($toAmount, 'subtract');
            } elseif ($this->type === self::TYPE_DONATION && $this->to_account_id) {
                $amount = $this->getConvertedAmountForAccount($this->toAccount);
                $this->toAccount->updateBalance($amount, 'subtract');
            }

            return $this->delete();
        } catch (\Exception $e) {
            throw new \Exception('Failed to restore balances: ' . $e->getMessage());
        }
    }
}
