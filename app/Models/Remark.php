<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Remark extends Model
{
    use HasFactory;

    protected $fillable = [
        'remarkable_type',
        'remarkable_id',
        'user_id',
        'type',
        'remark',
        'metadata',
        'is_internal',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_internal' => 'boolean',
    ];

    // Constants for remark types
    const TYPE_STATUS_UPDATE = 'status_update';
    const TYPE_ASSIGNMENT = 'assignment';
    const TYPE_PROGRESS = 'progress';
    const TYPE_COMPLETION = 'completion';
    const TYPE_CANCELLATION = 'cancellation';
    const TYPE_GENERAL = 'general';

    /**
     * Get the parent remarkable model (donation, beneficiary, etc.)
     */
    public function remarkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who made this remark
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_STATUS_UPDATE => 'Status Update',
            self::TYPE_ASSIGNMENT => 'Assignment',
            self::TYPE_PROGRESS => 'Progress Update',
            self::TYPE_COMPLETION => 'Completion',
            self::TYPE_CANCELLATION => 'Cancellation',
            self::TYPE_GENERAL => 'General',
            default => 'Unknown',
        };
    }

    /**
     * Get the formatted created date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('M j, Y g:i A');
    }

    /**
     * Scope for filtering by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for internal remarks
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope for public remarks
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Scope for remarks by a specific user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent remarks
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
