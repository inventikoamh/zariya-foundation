<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;
use Illuminate\Support\Facades\Storage;

class ComprehensiveAchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create achievement icons directory if it doesn't exist
        if (!Storage::disk('public')->exists('achievements/icons')) {
            Storage::disk('public')->makeDirectory('achievements/icons');
        }

        $achievements = [
            // DONATION ACHIEVEMENTS
            [
                'name' => 'First Steps',
                'description' => 'Made your very first donation to the foundation',
                'type' => 'donation',
                'category' => 'milestone',
                'icon_image' => 'achievements/icons/first-steps.png',
                'criteria' => [
                    'type' => 'donation_count',
                    'min_count' => 1,
                    'status' => 'completed',
                    'donation_type' => null
                ],
                'points' => 25,
                'rarity' => 'common',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Generous Heart',
                'description' => 'Completed 5 donations to help those in need',
                'type' => 'donation',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/generous-heart.png',
                'criteria' => [
                    'type' => 'donation_count',
                    'min_count' => 5,
                    'status' => 'completed',
                    'donation_type' => null
                ],
                'points' => 75,
                'rarity' => 'uncommon',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Dedicated Donor',
                'description' => 'Completed 10 donations, showing consistent commitment to helping others',
                'type' => 'donation',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/dedicated-donor.png',
                'criteria' => [
                    'type' => 'donation_count',
                    'min_count' => 10,
                    'status' => 'completed',
                    'donation_type' => null
                ],
                'points' => 150,
                'rarity' => 'rare',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Philanthropist',
                'description' => 'Completed 25 donations, demonstrating exceptional generosity',
                'type' => 'donation',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/philanthropist.png',
                'criteria' => [
                    'type' => 'donation_count',
                    'min_count' => 25,
                    'status' => 'completed',
                    'donation_type' => null
                ],
                'points' => 300,
                'rarity' => 'epic',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Big Heart',
                'description' => 'Made a single donation of $100 or more',
                'type' => 'donation',
                'category' => 'monetary',
                'icon_image' => 'achievements/icons/big-heart.png',
                'criteria' => [
                    'type' => 'donation_amount',
                    'min_amount' => 100.00,
                    'currency' => 'USD',
                    'donation_type' => 'monetary'
                ],
                'points' => 100,
                'rarity' => 'uncommon',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Major Contributor',
                'description' => 'Made a single donation of $500 or more',
                'type' => 'donation',
                'category' => 'monetary',
                'icon_image' => 'achievements/icons/major-contributor.png',
                'criteria' => [
                    'type' => 'donation_amount',
                    'min_amount' => 500.00,
                    'currency' => 'USD',
                    'donation_type' => 'monetary'
                ],
                'points' => 250,
                'rarity' => 'rare',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Benefactor',
                'description' => 'Made a single donation of $1000 or more',
                'type' => 'donation',
                'category' => 'monetary',
                'icon_image' => 'achievements/icons/benefactor.png',
                'criteria' => [
                    'type' => 'donation_amount',
                    'min_amount' => 1000.00,
                    'currency' => 'USD',
                    'donation_type' => 'monetary'
                ],
                'points' => 500,
                'rarity' => 'epic',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Material Helper',
                'description' => 'Made 3 materialistic donations (food, clothing, supplies)',
                'type' => 'donation',
                'category' => 'materialistic',
                'icon_image' => 'achievements/icons/material-helper.png',
                'criteria' => [
                    'type' => 'donation_count',
                    'min_count' => 3,
                    'status' => 'completed',
                    'donation_type' => 'materialistic'
                ],
                'points' => 80,
                'rarity' => 'uncommon',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Service Provider',
                'description' => 'Made 2 service donations (volunteer time, skills, expertise)',
                'type' => 'donation',
                'category' => 'service',
                'icon_image' => 'achievements/icons/service-provider.png',
                'criteria' => [
                    'type' => 'donation_count',
                    'min_count' => 2,
                    'status' => 'completed',
                    'donation_type' => 'service'
                ],
                'points' => 90,
                'rarity' => 'uncommon',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],

            // VOLUNTEER ACHIEVEMENTS
            [
                'name' => 'First Volunteer',
                'description' => 'Completed your first volunteer assignment',
                'type' => 'volunteer',
                'category' => 'milestone',
                'icon_image' => 'achievements/icons/first-volunteer.png',
                'criteria' => [
                    'type' => 'volunteer_completion',
                    'min_completions' => 1,
                    'assignment_type' => null
                ],
                'points' => 30,
                'rarity' => 'common',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Helpful Hand',
                'description' => 'Completed 5 volunteer assignments',
                'type' => 'volunteer',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/helpful-hand.png',
                'criteria' => [
                    'type' => 'volunteer_completion',
                    'min_completions' => 5,
                    'assignment_type' => null
                ],
                'points' => 100,
                'rarity' => 'uncommon',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Volunteer Hero',
                'description' => 'Completed 15 volunteer assignments, making a real difference',
                'type' => 'volunteer',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/volunteer-hero.png',
                'criteria' => [
                    'type' => 'volunteer_completion',
                    'min_completions' => 15,
                    'assignment_type' => null
                ],
                'points' => 200,
                'rarity' => 'rare',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Community Champion',
                'description' => 'Completed 30 volunteer assignments, showing exceptional dedication',
                'type' => 'volunteer',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/community-champion.png',
                'criteria' => [
                    'type' => 'volunteer_completion',
                    'min_completions' => 30,
                    'assignment_type' => null
                ],
                'points' => 400,
                'rarity' => 'epic',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Delivery Expert',
                'description' => 'Completed 10 delivery volunteer assignments',
                'type' => 'volunteer',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/delivery-expert.png',
                'criteria' => [
                    'type' => 'volunteer_completion',
                    'min_completions' => 10,
                    'assignment_type' => 'delivery'
                ],
                'points' => 120,
                'rarity' => 'uncommon',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Collection Master',
                'description' => 'Completed 8 collection volunteer assignments',
                'type' => 'volunteer',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/collection-master.png',
                'criteria' => [
                    'type' => 'volunteer_completion',
                    'min_completions' => 8,
                    'assignment_type' => 'collection'
                ],
                'points' => 110,
                'rarity' => 'uncommon',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],

            // STREAK ACHIEVEMENTS
            [
                'name' => 'Consistent Helper',
                'description' => 'Made donations for 3 consecutive days',
                'type' => 'donation',
                'category' => 'streak',
                'icon_image' => 'achievements/icons/consistent-helper.png',
                'criteria' => [
                    'type' => 'streak',
                    'min_streak' => 3,
                    'streak_type' => 'donation'
                ],
                'points' => 60,
                'rarity' => 'uncommon',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Daily Supporter',
                'description' => 'Made donations for 7 consecutive days',
                'type' => 'donation',
                'category' => 'streak',
                'icon_image' => 'achievements/icons/daily-supporter.png',
                'criteria' => [
                    'type' => 'streak',
                    'min_streak' => 7,
                    'streak_type' => 'donation'
                ],
                'points' => 150,
                'rarity' => 'rare',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Volunteer Streak',
                'description' => 'Completed volunteer assignments for 5 consecutive days',
                'type' => 'volunteer',
                'category' => 'streak',
                'icon_image' => 'achievements/icons/volunteer-streak.png',
                'criteria' => [
                    'type' => 'streak',
                    'min_streak' => 5,
                    'streak_type' => 'volunteer'
                ],
                'points' => 180,
                'rarity' => 'rare',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],

            // GENERAL ACHIEVEMENTS
            [
                'name' => 'Profile Complete',
                'description' => 'Completed your profile with all required information',
                'type' => 'general',
                'category' => 'milestone',
                'icon_image' => 'achievements/icons/profile-complete.png',
                'criteria' => [
                    'type' => 'milestone',
                    'milestone' => 'profile_complete'
                ],
                'points' => 20,
                'rarity' => 'common',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Account Verified',
                'description' => 'Successfully verified your account',
                'type' => 'general',
                'category' => 'milestone',
                'icon_image' => 'achievements/icons/account-verified.png',
                'criteria' => [
                    'type' => 'milestone',
                    'milestone' => 'account_verified'
                ],
                'points' => 40,
                'rarity' => 'common',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],

            // SPECIAL ACHIEVEMENTS
            [
                'name' => 'Birthday Giver',
                'description' => 'Made a donation on your birthday',
                'type' => 'donation',
                'category' => 'special',
                'icon_image' => 'achievements/icons/birthday-giver.png',
                'criteria' => [
                    'type' => 'special',
                    'special_type' => 'birthday_donation'
                ],
                'points' => 100,
                'rarity' => 'rare',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Holiday Helper',
                'description' => 'Made a donation during a holiday season',
                'type' => 'donation',
                'category' => 'special',
                'icon_image' => 'achievements/icons/holiday-helper.png',
                'criteria' => [
                    'type' => 'special',
                    'special_type' => 'holiday_donation'
                ],
                'points' => 120,
                'rarity' => 'rare',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Emergency Responder',
                'description' => 'Made a donation during an emergency response campaign',
                'type' => 'donation',
                'category' => 'special',
                'icon_image' => 'achievements/icons/emergency-responder.png',
                'criteria' => [
                    'type' => 'special',
                    'special_type' => 'emergency_response'
                ],
                'points' => 200,
                'rarity' => 'epic',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],

            // LEGENDARY ACHIEVEMENTS
            [
                'name' => 'Foundation Legend',
                'description' => 'Completed 50 donations, becoming a true legend of the foundation',
                'type' => 'donation',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/foundation-legend.png',
                'criteria' => [
                    'type' => 'donation_count',
                    'min_count' => 50,
                    'status' => 'completed',
                    'donation_type' => null
                ],
                'points' => 750,
                'rarity' => 'legendary',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Volunteer Legend',
                'description' => 'Completed 50 volunteer assignments, achieving legendary status',
                'type' => 'volunteer',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/volunteer-legend.png',
                'criteria' => [
                    'type' => 'volunteer_completion',
                    'min_completions' => 50,
                    'assignment_type' => null
                ],
                'points' => 800,
                'rarity' => 'legendary',
                'is_active' => true,
                'is_repeatable' => false,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'name' => 'Ultimate Benefactor',
                'description' => 'Made a single donation of $5000 or more',
                'type' => 'donation',
                'category' => 'monetary',
                'icon_image' => 'achievements/icons/ultimate-benefactor.png',
                'criteria' => [
                    'type' => 'donation_amount',
                    'min_amount' => 5000.00,
                    'currency' => 'USD',
                    'donation_type' => 'monetary'
                ],
                'points' => 1000,
                'rarity' => 'legendary',
                'is_active' => true,
                'is_repeatable' => true,
                'max_earnings' => null,
                'available_from' => null,
                'available_until' => null,
            ],
        ];

        foreach ($achievements as $achievementData) {
            Achievement::create($achievementData);
        }

        $this->command->info('Comprehensive achievements seeded successfully!');
    }
}
