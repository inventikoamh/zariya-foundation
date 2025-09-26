<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'category',
        'icon_image',
        'criteria',
        'points',
        'rarity',
        'is_active',
        'is_repeatable',
        'max_earnings',
        'available_from',
        'available_until',
    ];

    protected $casts = [
        'criteria' => 'array',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'is_active' => 'boolean',
        'is_repeatable' => 'boolean',
    ];

    // Relationships
    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
                    ->withPivot('earned_at', 'metadata', 'is_notified')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeAvailable($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('available_from')
              ->orWhere('available_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('available_until')
              ->orWhere('available_until', '>=', $now);
        });
    }

    public function scopeByRarity($query, $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    // Accessors
    public function getIconImageUrlAttribute()
    {
        return asset('storage/' . $this->icon_image);
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'donation' => 'Donation',
            'volunteer' => 'Volunteer',
            'general' => 'General',
            default => ucfirst($this->type),
        };
    }

    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'monetary' => 'Monetary',
            'materialistic' => 'Materialistic',
            'service' => 'Service',
            'completion' => 'Completion',
            'milestone' => 'Milestone',
            'streak' => 'Streak',
            'special' => 'Special',
            default => ucfirst($this->category),
        };
    }

    public function getRarityLabelAttribute()
    {
        return match($this->rarity) {
            'common' => 'Common',
            'uncommon' => 'Uncommon',
            'rare' => 'Rare',
            'epic' => 'Epic',
            'legendary' => 'Legendary',
            default => ucfirst($this->rarity),
        };
    }

    public function getRarityColorAttribute()
    {
        return match($this->rarity) {
            'common' => 'gray',
            'uncommon' => 'green',
            'rare' => 'blue',
            'epic' => 'purple',
            'legendary' => 'gold',
            default => 'gray',
        };
    }

    // Methods
    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->available_from && $this->available_from > $now) {
            return false;
        }

        if ($this->available_until && $this->available_until < $now) {
            return false;
        }

        return true;
    }

    public function canBeEarnedBy(User $user)
    {
        if (!$this->isAvailable()) {
            return false;
        }

        // Check if user already has this achievement
        $userAchievement = $this->userAchievements()
            ->where('user_id', $user->id)
            ->first();

        if ($userAchievement && !$this->is_repeatable) {
            return false;
        }

        // Check max earnings limit
        if ($this->max_earnings) {
            $earnedCount = $this->userAchievements()
                ->where('user_id', $user->id)
                ->count();

            if ($earnedCount >= $this->max_earnings) {
                return false;
            }
        }

        return true;
    }

    public function getProgressForUser(User $user)
    {
        // This method will be implemented in the AchievementService
        // to calculate progress towards this achievement
        return 0;
    }
}
