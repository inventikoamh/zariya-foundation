<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\EmailSetting;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        EmailTemplate::truncate();
        EmailSetting::truncate();

        // Create basic templates
        $templates = [
            [
                'type' => 'registration',
                'name' => 'Welcome Email',
                'subject' => 'Welcome to {{foundation_name}}! 🎉',
                'body_html' => '<h1>Welcome {{user_name}}!</h1><p>Thank you for joining {{foundation_name}}!</p>',
                'variables' => ['user_name', 'user_email', 'login_url', 'foundation_name'],
            ],
            [
                'type' => 'donation',
                'name' => 'Donation Confirmation',
                'subject' => 'Thank you for your donation! 💝',
                'body_html' => '<h1>Thank you {{user_name}}!</h1><p>Your donation of ${{donation_amount}} has been received.</p>',
                'variables' => ['user_name', 'donation_amount', 'donation_type', 'donation_date', 'foundation_name'],
            ],
            [
                'type' => 'achievement_earned',
                'name' => 'Achievement Earned',
                'subject' => '🏆 New Achievement Earned!',
                'body_html' => '<h1>Congratulations {{user_name}}!</h1><p>You earned: {{achievement_name}}</p>',
                'variables' => ['user_name', 'achievement_name', 'achievement_description', 'achievement_points', 'achievement_rarity', 'foundation_name'],
            ],
            [
                'type' => 'profile_update',
                'name' => 'Profile Update',
                'subject' => 'Profile Updated ✅',
                'body_html' => '<h1>Profile Updated</h1><p>Your profile was updated on {{update_date}}.</p>',
                'variables' => ['user_name', 'update_date', 'foundation_name'],
            ],
            [
                'type' => 'achievement_created',
                'name' => 'New Achievement Available',
                'subject' => '🎯 New Achievement: {{achievement_name}}',
                'body_html' => '<h1>New Achievement Available!</h1><p>{{achievement_name}}: {{achievement_description}}</p>',
                'variables' => ['user_name', 'achievement_name', 'achievement_description', 'foundation_name'],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create(array_merge($template, ['is_active' => true]));
        }

        // Create settings
        $settings = [
            ['type' => 'registration', 'name' => 'Welcome Email'],
            ['type' => 'donation', 'name' => 'Donation Confirmation'],
            ['type' => 'achievement_earned', 'name' => 'Achievement Earned'],
            ['type' => 'profile_update', 'name' => 'Profile Update'],
            ['type' => 'achievement_created', 'name' => 'New Achievement Available'],
        ];

        foreach ($settings as $setting) {
            EmailSetting::create(array_merge($setting, [
                'enabled' => true,
                'settings' => ['delay_minutes' => 0, 'retry_attempts' => 3, 'priority' => 'normal'],
            ]));
        }
    }
}
