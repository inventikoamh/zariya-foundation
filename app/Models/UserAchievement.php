<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'achievement_id',
        'earned_at',
        'metadata',
        'is_notified',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'metadata' => 'array',
        'is_notified' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('earned_at', '>=', now()->subDays($days));
    }

    public function scopeNotNotified($query)
    {
        return $query->where('is_notified', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->whereHas('achievement', function ($q) use ($type) {
            $q->where('type', $type);
        });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->whereHas('achievement', function ($q) use ($category) {
            $q->where('category', $category);
        });
    }

    public function scopeByRarity($query, $rarity)
    {
        return $query->whereHas('achievement', function ($q) use ($rarity) {
            $q->where('rarity', $rarity);
        });
    }

    // Accessors
    public function getFormattedEarnedAtAttribute()
    {
        return $this->earned_at->format('M j, Y');
    }

    public function getTimeAgoAttribute()
    {
        return $this->earned_at->diffForHumans();
    }

    // Methods
    public function markAsNotified()
    {
        $this->update(['is_notified' => true]);
    }

    public function getMetadataValue($key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    public function setMetadataValue($key, $value)
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->update(['metadata' => $metadata]);
    }

    /**
     * Get the morph class for this model.
     */
    public function getMorphClass()
    {
        return 'user_achievement';
    }
}
