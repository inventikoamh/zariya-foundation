<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginMethodSetting extends Model
{
    protected $fillable = [
        'method',
        'is_enabled',
        'display_name',
        'description',
        'settings'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'settings' => 'array'
    ];

    /**
     * Get enabled login methods
     */
    public static function getEnabledMethods()
    {
        return self::where('is_enabled', true)->get();
    }

    /**
     * Check if a specific method is enabled
     */
    public static function isMethodEnabled($method)
    {
        return self::where('method', $method)
                   ->where('is_enabled', true)
                   ->exists();
    }

    /**
     * Get available login methods for display
     */
    public static function getAvailableMethods()
    {
        return self::where('is_enabled', true)
                   ->pluck('display_name', 'method')
                   ->toArray();
    }

    /**
     * Get login method configuration
     */
    public static function getMethodConfig($method)
    {
        return self::where('method', $method)->first();
    }
}
