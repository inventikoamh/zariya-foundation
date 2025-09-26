<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'details',
        'status',
        'donor_id',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'address',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'completed_at',
        'completion_notes',
        'notes',
        'is_urgent',
        'priority',
        'account_id',
    ];

    protected $casts = [
        'details' => 'array',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_urgent' => 'boolean',
    ];

    // Constants for donation types
    const TYPE_MONETARY = 'monetary';
    const TYPE_MATERIALISTIC = 'materialistic';
    const TYPE_SERVICE = 'service';

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';

    // Constants for priority
    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;
    const PRIORITY_CRITICAL = 4;

    /**
     * Get the donor who made this donation
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    /**
     * Get the volunteer assigned to this donation
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who assigned this donation
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the country for this donation
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state for this donation
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city for this donation
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the account for this donation (for monetary donations)
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get all remarks for this donation
     */
    public function remarks(): MorphMany
    {
        return $this->morphMany(Remark::class, 'remarkable')->orderBy('created_at', 'desc');
    }

    /**
     * Get all transactions for this donation
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all donation histories for this donation
     */
    public function donationHistories(): HasMany
    {
        return $this->hasMany(DonationHistory::class);
    }

    /**
     * Get total beneficiaries helped by this donation
     */
    public function getTotalBeneficiariesHelpedAttribute(): int
    {
        return $this->donationHistories()->distinct('beneficiary_id')->count();
    }

    /**
     * Get total amount provided from this donation
     */
    public function getTotalAmountProvidedAttribute(): float
    {
        return $this->donationHistories()->monetary()->sum('amount') ?? 0;
    }

    /**
     * Get total quantity provided from this donation
     */
    public function getTotalQuantityProvidedAttribute(): int
    {
        return $this->donationHistories()->materialistic()->sum('quantity') ?? 0;
    }

    /**
     * Get total service instances provided from this donation
     */
    public function getTotalServiceInstancesAttribute(): int
    {
        return $this->donationHistories()->service()->count();
    }

    /**
     * Get public remarks (visible to donor)
     */
    public function publicRemarks(): MorphMany
    {
        return $this->morphMany(Remark::class, 'remarkable')->where('is_internal', false)->orderBy('created_at', 'desc');
    }

    /**
     * Get internal remarks (not visible to donor)
     */
    public function internalRemarks(): MorphMany
    {
        return $this->morphMany(Remark::class, 'remarkable')->where('is_internal', true)->orderBy('created_at', 'desc');
    }

    /**
     * Get the formatted location string
     */
    public function getLocationAttribute(): string
    {
        $location = [];

        if ($this->city) {
            $location[] = $this->city->name;
        }
        if ($this->state) {
            $location[] = $this->state->name;
        }
        if ($this->country) {
            $location[] = $this->country->name;
        }

        return implode(', ', $location);
    }

    /**
     * Get the priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        try {
            $label = match($this->priority) {
                self::PRIORITY_LOW => 'Low',
                self::PRIORITY_MEDIUM => 'Medium',
                self::PRIORITY_HIGH => 'High',
                self::PRIORITY_CRITICAL => 'Critical',
                default => 'Unknown',
            };
            return is_string($label) ? $label : 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        try {
            $label = \App\Services\StatusHelper::getStatusDisplayName($this->status, $this->type);
            return is_string($label) ? $label : 'Unknown Status';
        } catch (\Exception $e) {
            return 'Unknown Status';
        }
    }

    /**
     * Get the type label
     */
    public function getTypeLabelAttribute(): string
    {
        try {
            $label = match($this->type) {
                self::TYPE_MONETARY => 'Monetary',
                self::TYPE_MATERIALISTIC => 'Materialistic',
                self::TYPE_SERVICE => 'Service',
                default => 'Unknown',
            };
            return is_string($label) ? $label : 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Check if donation is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if donation is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if donation is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if donation is active (not completed, cancelled, or rejected)
     */
    public function isActive(): bool
    {
        return !in_array($this->status, [
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_REJECTED
        ]);
    }

    /**
     * Get monetary donation amount
     */
    public function getAmountAttribute(): ?float
    {
        if ($this->type === self::TYPE_MONETARY && isset($this->details['amount'])) {
            return (float) $this->details['amount'];
        }
        return null;
    }

    /**
     * Get monetary donation currency
     */
    public function getCurrencyAttribute(): ?string
    {
        if ($this->type === self::TYPE_MONETARY && isset($this->details['currency'])) {
            return $this->details['currency'];
        }
        return 'USD'; // Default currency
    }

    /**
     * Get formatted amount for display
     */
    public function getFormattedAmountAttribute(): ?string
    {
        if ($this->type === self::TYPE_MONETARY && $this->amount) {
            return number_format($this->amount, 2) . ' ' . $this->currency;
        }
        return null;
    }

    /**
     * Get materialistic donation item name
     */
    public function getItemNameAttribute(): ?string
    {
        if ($this->type === self::TYPE_MATERIALISTIC && isset($this->details['item_name'])) {
            return $this->details['item_name'];
        }
        return null;
    }

    /**
     * Get service donation service type
     */
    public function getServiceTypeAttribute(): ?string
    {
        if ($this->type === self::TYPE_SERVICE && isset($this->details['service_type'])) {
            return $this->details['service_type'];
        }
        return null;
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
     * Scope for filtering by priority
     */
    public function scopeWithPriority($query, int $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for urgent donations
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Scope for active donations
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_REJECTED
        ]);
    }

    /**
     * Scope for donations assigned to a specific user
     */
    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope for donations by a specific donor
     */
    public function scopeByDonor($query, int $donorId)
    {
        return $query->where('donor_id', $donorId);
    }

    /**
     * Relationship with donor certificates
     */
    public function certificates()
    {
        return $this->hasMany(DonorCertificate::class);
    }

    /**
     * Check if donation is eligible for certificate generation
     */
    public function isEligibleForCertificate()
    {
        if ($this->type === 'monetary') {
            return $this->status === 'completed';
        } elseif (in_array($this->type, ['materialistic', 'service'])) {
            return $this->status === 'donated';
        }

        return false;
    }
}
