<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Donation Achievements
        $this->createDonationAchievements();

        // Volunteer Achievements
        $this->createVolunteerAchievements();

        // General Achievements
        $this->createGeneralAchievements();
    }

    private function createDonationAchievements()
    {
        // First Donation
        Achievement::create([
            'name' => 'First Steps',
            'description' => 'Made your first donation to the foundation',
            'type' => 'donation',
            'category' => 'milestone',
            'icon_image' => 'achievements/icons/first-donation.png',
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'donation_count',
                'count' => 1
            ],
            'points' => 10,
            'rarity' => 'common',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        // Monetary Donation Achievements
        Achievement::create([
            'name' => 'Generous Donor',
            'description' => 'Donated ₹10,000 or more in monetary donations',
            'type' => 'donation',
            'category' => 'monetary',
            'icon_image' => 'achievements/icons/generous-donor.png',
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'donation_amount',
                'min_amount' => 10000,
                'donation_type' => 'monetary'
            ],
            'points' => 50,
            'rarity' => 'rare',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        Achievement::create([
            'name' => 'Philanthropist',
            'description' => 'Donated ₹50,000 or more in monetary donations',
            'type' => 'donation',
            'category' => 'monetary',
            'icon_image' => 'achievements/icons/philanthropist.png',
            'criteria' => [
                'type' => 'donation_amount',
                'min_amount' => 50000,
                'currency' => 'INR',
                'donation_type' => 'monetary'
            ],
            'points' => 100,
            'rarity' => 'epic',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        Achievement::create([
            'name' => 'Benefactor',
            'description' => 'Donated ₹1,00,000 or more in monetary donations',
            'type' => 'donation',
            'category' => 'monetary',
            'icon_image' => 'achievements/icons/benefactor.png',
            'criteria' => [
                'type' => 'donation_amount',
                'min_amount' => 100000,
                'currency' => 'INR',
                'donation_type' => 'monetary'
            ],
            'points' => 250,
            'rarity' => 'legendary',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        // Donation Count Achievements
        Achievement::create([
            'name' => 'Regular Supporter',
            'description' => 'Completed 5 donations',
            'type' => 'donation',
            'category' => 'completion',
            'icon_image' => 'achievements/icons/regular-supporter.png',
            'criteria' => [
                'type' => 'donation_count',
                'min_count' => 5,
                'donation_type' => null,
                'status' => 'completed'
            ],
            'points' => 25,
            'rarity' => 'uncommon',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        Achievement::create([
            'name' => 'Dedicated Donor',
            'description' => 'Completed 10 donations',
            'type' => 'donation',
            'category' => 'completion',
            'icon_image' => 'achievements/icons/dedicated-donor.png',
            'criteria' => [
                'type' => 'donation_count',
                'min_count' => 10,
                'donation_type' => null,
                'status' => 'completed'
            ],
            'points' => 50,
            'rarity' => 'rare',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        Achievement::create([
            'name' => 'Champion of Giving',
            'description' => 'Completed 25 donations',
            'type' => 'donation',
            'category' => 'completion',
            'icon_image' => 'achievements/icons/champion-giving.png',
            'criteria' => [
                'type' => 'donation_count',
                'min_count' => 25,
                'donation_type' => null,
                'status' => 'completed'
            ],
            'points' => 100,
            'rarity' => 'epic',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        // Type-specific Achievements
        Achievement::create([
            'name' => 'Material Helper',
            'description' => 'Completed 3 materialistic donations',
            'type' => 'donation',
            'category' => 'materialistic',
            'icon_image' => 'achievements/icons/material-helper.png',
            'criteria' => [
                'type' => 'donation_count',
                'min_count' => 3,
                'donation_type' => 'materialistic',
                'status' => 'donated'
            ],
            'points' => 30,
            'rarity' => 'uncommon',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        Achievement::create([
            'name' => 'Service Provider',
            'description' => 'Completed 3 service donations',
            'type' => 'donation',
            'category' => 'service',
            'icon_image' => 'achievements/icons/service-provider.png',
            'criteria' => [
                'type' => 'donation_count',
                'min_count' => 3,
                'donation_type' => 'service',
                'status' => 'donated'
            ],
            'points' => 30,
            'rarity' => 'uncommon',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        // Mixed Type Achievement
        Achievement::create([
            'name' => 'Versatile Giver',
            'description' => 'Made donations of all three types (monetary, materialistic, service)',
            'type' => 'donation',
            'category' => 'completion',
            'icon_image' => 'achievements/icons/versatile-giver.png',
            'criteria' => [
                'type' => 'donation_type_count',
                'type_counts' => [
                    'monetary' => 1,
                    'materialistic' => 1,
                    'service' => 1
                ],
                'status' => 'completed'
            ],
            'points' => 75,
            'rarity' => 'rare',
            'is_active' => true,
            'is_repeatable' => false,
        ]);
    }

    private function createVolunteerAchievements()
    {
        // First Volunteer Assignment
        Achievement::create([
            'name' => 'Volunteer Spirit',
            'description' => 'Completed your first volunteer assignment',
            'type' => 'volunteer',
            'category' => 'milestone',
            'icon_image' => 'achievements/icons/volunteer-spirit.png',
            'criteria' => [
                'type' => 'volunteer_completion',
                'min_completions' => 1,
                'assignment_type' => null
            ],
            'points' => 15,
            'rarity' => 'common',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        // Volunteer Completion Achievements
        Achievement::create([
            'name' => 'Active Volunteer',
            'description' => 'Completed 5 volunteer assignments',
            'type' => 'volunteer',
            'category' => 'completion',
            'icon_image' => 'achievements/icons/active-volunteer.png',
            'criteria' => [
                'type' => 'volunteer_completion',
                'min_completions' => 5,
                'assignment_type' => null
            ],
            'points' => 40,
            'rarity' => 'uncommon',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        Achievement::create([
            'name' => 'Dedicated Volunteer',
            'description' => 'Completed 10 volunteer assignments',
            'type' => 'volunteer',
            'category' => 'completion',
            'icon_image' => 'achievements/icons/dedicated-volunteer.png',
            'criteria' => [
                'type' => 'volunteer_completion',
                'min_completions' => 10,
                'assignment_type' => null
            ],
            'points' => 75,
            'rarity' => 'rare',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        Achievement::create([
            'name' => 'Volunteer Champion',
            'description' => 'Completed 25 volunteer assignments',
            'type' => 'volunteer',
            'category' => 'completion',
            'icon_image' => 'achievements/icons/volunteer-champion.png',
            'criteria' => [
                'type' => 'volunteer_completion',
                'min_completions' => 25,
                'assignment_type' => null
            ],
            'points' => 150,
            'rarity' => 'epic',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        // Beneficiary Help Achievement
        Achievement::create([
            'name' => 'Helper of Many',
            'description' => 'Helped 10 beneficiaries through volunteer work',
            'type' => 'volunteer',
            'category' => 'beneficiary_help',
            'icon_image' => 'achievements/icons/helper-many.png',
            'criteria' => [
                'type' => 'beneficiary_help',
                'min_helped' => 10,
                'status' => 'completed'
            ],
            'points' => 100,
            'rarity' => 'epic',
            'is_active' => true,
            'is_repeatable' => false,
        ]);
    }

    private function createGeneralAchievements()
    {
        // Profile Completion
        Achievement::create([
            'name' => 'Complete Profile',
            'description' => 'Completed your profile with all required information',
            'type' => 'general',
            'category' => 'milestone',
            'icon_image' => 'achievements/icons/complete-profile.png',
            'criteria' => [
                'type' => 'milestone',
                'milestone' => 'profile_complete'
            ],
            'points' => 5,
            'rarity' => 'common',
            'is_active' => true,
            'is_repeatable' => false,
        ]);

        // Special Achievements
        Achievement::create([
            'name' => 'Birthday Giver',
            'description' => 'Made a donation on your birthday',
            'type' => 'general',
            'category' => 'special',
            'icon_image' => 'achievements/icons/birthday-giver.png',
            'criteria' => [
                'type' => 'special',
                'special_type' => 'birthday_donation'
            ],
            'points' => 25,
            'rarity' => 'uncommon',
            'is_active' => true,
            'is_repeatable' => true,
            'max_earnings' => 1,
        ]);

        Achievement::create([
            'name' => 'Holiday Helper',
            'description' => 'Made a donation during a holiday',
            'type' => 'general',
            'category' => 'special',
            'icon_image' => 'achievements/icons/holiday-helper.png',
            'criteria' => [
                'type' => 'special',
                'special_type' => 'holiday_donation'
            ],
            'points' => 20,
            'rarity' => 'uncommon',
            'is_active' => true,
            'is_repeatable' => true,
        ]);

        Achievement::create([
            'name' => 'Big Heart',
            'description' => 'Made a large donation (₹25,000 or more)',
            'type' => 'general',
            'category' => 'special',
            'icon_image' => 'achievements/icons/big-heart.png',
            'criteria' => [
                'type' => 'special',
                'special_type' => 'large_donation'
            ],
            'points' => 50,
            'rarity' => 'rare',
            'is_active' => true,
            'is_repeatable' => true,
        ]);
    }
}
