<?php

namespace Database\Seeders;

use App\Models\CountryPhoneValidation;
use Illuminate\Database\Seeder;

class CountryPhoneValidationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phoneValidations = [
            [
                'country_code' => 'IND',
                'country_name' => 'India',
                'phone_code' => '+91',
                'min_digits' => 10,
                'max_digits' => 10,
                'phone_format' => 'XXXXX-XXXXX',
                'validation_regex' => '/^[6-9][0-9]{9}$/',
                'description' => 'Indian mobile numbers start with 6, 7, 8, or 9 and are exactly 10 digits',
                'is_active' => true
            ],
            [
                'country_code' => 'USA',
                'country_name' => 'United States',
                'phone_code' => '+1',
                'min_digits' => 10,
                'max_digits' => 10,
                'phone_format' => 'XXX-XXX-XXXX',
                'validation_regex' => '/^[2-9][0-9]{2}[2-9][0-9]{2}[0-9]{4}$/',
                'description' => 'US phone numbers are exactly 10 digits, area code cannot start with 0 or 1',
                'is_active' => true
            ],
            [
                'country_code' => 'GBR',
                'country_name' => 'United Kingdom',
                'phone_code' => '+44',
                'min_digits' => 10,
                'max_digits' => 11,
                'phone_format' => 'XXXX XXX XXXX',
                'validation_regex' => '/^[1-9][0-9]{9,10}$/',
                'description' => 'UK phone numbers are 10-11 digits, cannot start with 0',
                'is_active' => true
            ],
            [
                'country_code' => 'CAN',
                'country_name' => 'Canada',
                'phone_code' => '+1',
                'min_digits' => 10,
                'max_digits' => 10,
                'phone_format' => 'XXX-XXX-XXXX',
                'validation_regex' => '/^[2-9][0-9]{2}[2-9][0-9]{2}[0-9]{4}$/',
                'description' => 'Canadian phone numbers are exactly 10 digits, similar to US format',
                'is_active' => true
            ],
            [
                'country_code' => 'AUS',
                'country_name' => 'Australia',
                'phone_code' => '+61',
                'min_digits' => 9,
                'max_digits' => 9,
                'phone_format' => 'XXXX XXX XXX',
                'validation_regex' => '/^[2-9][0-9]{8}$/',
                'description' => 'Australian mobile numbers are 9 digits, cannot start with 0 or 1',
                'is_active' => true
            ],
            [
                'country_code' => 'DEU',
                'country_name' => 'Germany',
                'phone_code' => '+49',
                'min_digits' => 10,
                'max_digits' => 12,
                'phone_format' => 'XXX XXXXXXXX',
                'validation_regex' => '/^[1-9][0-9]{9,11}$/',
                'description' => 'German phone numbers are 10-12 digits, cannot start with 0',
                'is_active' => true
            ],
            [
                'country_code' => 'FRA',
                'country_name' => 'France',
                'phone_code' => '+33',
                'min_digits' => 9,
                'max_digits' => 9,
                'phone_format' => 'X XX XX XX XX',
                'validation_regex' => '/^[1-9][0-9]{8}$/',
                'description' => 'French mobile numbers are 9 digits, cannot start with 0',
                'is_active' => true
            ],
            [
                'country_code' => 'JPN',
                'country_name' => 'Japan',
                'phone_code' => '+81',
                'min_digits' => 10,
                'max_digits' => 11,
                'phone_format' => 'XX-XXXX-XXXX',
                'validation_regex' => '/^[0-9]{10,11}$/',
                'description' => 'Japanese phone numbers are 10-11 digits',
                'is_active' => true
            ],
            [
                'country_code' => 'CHN',
                'country_name' => 'China',
                'phone_code' => '+86',
                'min_digits' => 11,
                'max_digits' => 11,
                'phone_format' => 'XXX XXXX XXXX',
                'validation_regex' => '/^1[3-9][0-9]{9}$/',
                'description' => 'Chinese mobile numbers are exactly 11 digits, start with 1',
                'is_active' => true
            ],
            [
                'country_code' => 'BRA',
                'country_name' => 'Brazil',
                'phone_code' => '+55',
                'min_digits' => 10,
                'max_digits' => 11,
                'phone_format' => 'XX XXXXX-XXXX',
                'validation_regex' => '/^[1-9][0-9]{9,10}$/',
                'description' => 'Brazilian mobile numbers are 10-11 digits, cannot start with 0',
                'is_active' => true
            ]
        ];

        foreach ($phoneValidations as $validation) {
            CountryPhoneValidation::firstOrCreate(
                ['country_code' => $validation['country_code']],
                $validation
            );
        }
    }
}
