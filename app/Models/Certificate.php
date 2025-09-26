<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'template_image',
        'name_position',
        'name_font_family',
        'name_font_size',
        'name_font_color',
        'name_bold',
        'name_italic',
        'date_position',
        'date_font_family',
        'date_font_size',
        'date_font_color',
        'amount_position',
        'amount_font_family',
        'amount_font_size',
        'amount_font_color',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'name_position' => 'array',
        'date_position' => 'array',
        'amount_position' => 'array',
        'name_bold' => 'boolean',
        'name_italic' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Static methods
    public static function getDefaultForType($type)
    {
        return static::active()
            ->byType($type)
            ->default()
            ->first();
    }

    public static function getActiveForType($type)
    {
        return static::active()
            ->byType($type)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Accessors
    public function getTemplateImageUrlAttribute()
    {
        return asset('storage/' . $this->template_image);
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'monetary' => 'Monetary Donation',
            'materialistic' => 'Materialistic Donation',
            'service' => 'Service Donation',
            'general' => 'General',
            default => ucfirst($this->type),
        };
    }
}
