<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'type',
        'color',
        'is_fixed',
        'is_active',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFixed($query)
    {
        return $query->where('is_fixed', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    // Static methods for getting statuses by type
    public static function getMonetaryStatuses()
    {
        return self::active()->byType('monetary')->ordered()->get();
    }

    public static function getBeneficiaryStatuses()
    {
        return self::active()->byType('beneficiary')->ordered()->get();
    }

    public static function getMaterialisticStatuses()
    {
        return self::active()->byType('materialistic')->ordered()->get();
    }

    public static function getServiceStatuses()
    {
        return self::active()->byType('service')->ordered()->get();
    }

    // Get status options for forms
    public static function getStatusOptions(string $type)
    {
        return self::active()->byType($type)->ordered()->pluck('display_name', 'name')->toArray();
    }

    // Check if status can be deleted
    public function canBeDeleted()
    {
        return !$this->is_fixed && !$this->isInUse();
    }

    // Check if status is in use
    public function isInUse()
    {
        // Check if status is used in donations (all types)
        $donationCount = Donation::where('status', $this->name)->count();

        // Check if status is used in beneficiaries
        $beneficiaryCount = Beneficiary::where('status', $this->name)->count();

        return $donationCount > 0 || $beneficiaryCount > 0;
    }

    // Get CSS class for status badge
    public function getBadgeClassAttribute()
    {
        $colorMap = [
            '#EF4444' => 'bg-red-100 text-red-800', // Red
            '#F59E0B' => 'bg-yellow-100 text-yellow-800', // Yellow
            '#10B981' => 'bg-green-100 text-green-800', // Green
            '#3B82F6' => 'bg-blue-100 text-blue-800', // Blue
            '#8B5CF6' => 'bg-purple-100 text-purple-800', // Purple
            '#F97316' => 'bg-orange-100 text-orange-800', // Orange
            '#6B7280' => 'bg-gray-100 text-gray-800', // Gray
        ];

        return $colorMap[$this->color] ?? 'bg-gray-100 text-gray-800';
    }
}
