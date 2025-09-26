<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assignment_type',
        'country_id',
        'state_id',
        'city_id',
        'role',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country for the assignment.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state for the assignment.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city for the assignment.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Scope to get only active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get head volunteers.
     */
    public function scopeHeadVolunteers($query)
    {
        return $query->where('role', 'head_volunteer');
    }

    /**
     * Get the region name based on assignment type.
     */
    public function getRegionNameAttribute(): string
    {
        return match ($this->assignment_type) {
            'country' => $this->country?->name ?? 'Unknown',
            'state' => $this->state?->name ?? 'Unknown',
            'city' => $this->city?->name ?? 'Unknown',
            default => 'Unknown',
        };
    }
}