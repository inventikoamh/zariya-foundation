<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key','value','type'];

    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        return $row ? static::castOut($row->value, $row->type) : $default;
    }

    public static function set(string $key, $value, string $type = 'string'): void
    {
        static::updateOrCreate(['key' => $key], [
            'value' => static::castIn($value, $type),
            'type' => $type,
        ]);
    }

    private static function castIn($value, string $type)
    {
        return match ($type) {
            'json' => json_encode($value),
            default => (string) $value,
        };
    }

    private static function castOut($value, string $type)
    {
        return match ($type) {
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Get the morph class for this model.
     */
    public function getMorphClass()
    {
        return 'system_setting';
    }
}



