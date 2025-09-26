<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryPhoneValidation extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'phone_code',
        'min_digits',
        'max_digits',
        'phone_format',
        'validation_regex',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_digits' => 'integer',
        'max_digits' => 'integer'
    ];

    /**
     * Get validation rules for a specific country
     */
    public static function getValidationRules($countryCode)
    {
        $validation = self::where('country_code', $countryCode)
                         ->where('is_active', true)
                         ->first();

        if (!$validation) {
            return [
                'min' => 10,
                'max' => 15,
                'regex' => '/^[0-9]{10,15}$/'
            ];
        }

        return [
            'min' => $validation->min_digits,
            'max' => $validation->max_digits,
            'regex' => $validation->validation_regex ?: '/^[0-9]{' . $validation->min_digits . ',' . $validation->max_digits . '}$/'
        ];
    }

    /**
     * Validate phone number for a specific country
     */
    public static function validatePhone($phone, $countryCode)
    {
        $rules = self::getValidationRules($countryCode);
        
        if (strlen($phone) < $rules['min'] || strlen($phone) > $rules['max']) {
            return false;
        }

        if ($rules['regex'] && !preg_match($rules['regex'], $phone)) {
            return false;
        }

        return true;
    }
}
