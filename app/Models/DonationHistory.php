<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_id',
        'donation_id',
        'provided_by',
        'donation_type',
        'amount',
        'currency',
        'exchange_rate',
        'converted_amount',
        'converted_currency',
        'account_id',
        'quantity',
        'unit',
        'description',
        'status',
        'notes',
        'provided_at',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'converted_amount' => 'decimal:2',
        'quantity' => 'integer',
        'provided_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function providedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provided_by');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // Scopes
    public function scopeMonetary($query)
    {
        return $query->where('donation_type', 'monetary');
    }

    public function scopeMaterialistic($query)
    {
        return $query->where('donation_type', 'materialistic');
    }

    public function scopeService($query)
    {
        return $query->where('donation_type', 'service');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        if ($this->donation_type === 'monetary' && $this->amount) {
            return number_format($this->amount, 2) . ' ' . ($this->currency ?? 'USD');
        }
        return null;
    }

    public function getFormattedConvertedAmountAttribute()
    {
        if ($this->donation_type === 'monetary' && $this->converted_amount) {
            return number_format($this->converted_amount, 2) . ' ' . ($this->converted_currency ?? 'USD');
        }
        return null;
    }

    public function getFormattedQuantityAttribute()
    {
        if ($this->donation_type === 'materialistic' && $this->quantity) {
            return $this->quantity . ' ' . ($this->unit ?? 'items');
        }
        return null;
    }
}
