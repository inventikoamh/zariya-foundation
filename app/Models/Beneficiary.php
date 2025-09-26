<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'category',
        'description',
        'urgency_notes',
        'status',
        'priority',
        'estimated_amount',
        'currency',
        'location',
        'additional_info',
        'requested_by',
        'assigned_to',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'location' => 'array',
        'additional_info' => 'array',
        'estimated_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FULFILLED = 'fulfilled';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Category constants
    const CATEGORY_MEDICAL = 'medical';
    const CATEGORY_EDUCATION = 'education';
    const CATEGORY_FOOD = 'food';
    const CATEGORY_SHELTER = 'shelter';
    const CATEGORY_EMERGENCY = 'emergency';
    const CATEGORY_OTHER = 'other';

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function remarks(): MorphMany
    {
        return $this->morphMany(Remark::class, 'remarkable');
    }

    public function donationHistories(): HasMany
    {
        return $this->hasMany(DonationHistory::class);
    }

    public function monetaryDonations(): HasMany
    {
        return $this->donationHistories()->monetary();
    }

    public function materialisticDonations(): HasMany
    {
        return $this->donationHistories()->materialistic();
    }

    public function serviceDonations(): HasMany
    {
        return $this->donationHistories()->service();
    }

    public function approvedDonations(): HasMany
    {
        return $this->donationHistories()->approved();
    }

    public function completedDonations(): HasMany
    {
        return $this->donationHistories()->completed();
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_UNDER_REVIEW => 'bg-blue-100 text-blue-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_FULFILLED => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'bg-gray-100 text-gray-800',
            self::PRIORITY_MEDIUM => 'bg-blue-100 text-blue-800',
            self::PRIORITY_HIGH => 'bg-orange-100 text-orange-800',
            self::PRIORITY_URGENT => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getCategoryOptions(): array
    {
        return [
            self::CATEGORY_MEDICAL => 'Medical Assistance',
            self::CATEGORY_EDUCATION => 'Education Support',
            self::CATEGORY_FOOD => 'Food & Nutrition',
            self::CATEGORY_SHELTER => 'Shelter & Housing',
            self::CATEGORY_EMERGENCY => 'Emergency Relief',
            self::CATEGORY_OTHER => 'Other',
        ];
    }

    public function getPriorityOptions(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    public function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_FULFILLED => 'Fulfilled',
        ];
    }
}
