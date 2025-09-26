<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoginMethodSetting;

class LoginMethodSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Password Login Method
        LoginMethodSetting::firstOrCreate(
            ['method' => 'password'],
            [
                'is_enabled' => true,
                'display_name' => 'Password Login',
                'description' => 'Login using phone number and password',
                'settings' => [
                    'require_phone_verification' => false,
                    'allow_remember_me' => true,
                    'max_login_attempts' => 5,
                    'lockout_duration' => 15 // minutes
                ]
            ]
        );

        // SMS Login Method
        LoginMethodSetting::firstOrCreate(
            ['method' => 'sms'],
            [
                'is_enabled' => false,
                'display_name' => 'SMS Login',
                'description' => 'Login using phone number and SMS OTP',
                'settings' => [
                    'otp_length' => 6,
                    'otp_expiry' => 5, // minutes
                    'max_otp_attempts' => 3,
                    'resend_cooldown' => 60 // seconds
                ]
            ]
        );
    }
}
