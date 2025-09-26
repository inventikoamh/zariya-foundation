<?php

/**
 * 🏆 Achievement System - Complete Usage Examples
 *
 * This file contains comprehensive examples of how to use the achievement system
 * in your Laravel application. Copy and adapt these examples for your specific needs.
 */

use App\Services\EnhancedAchievementService;
use App\Models\Achievement;
use App\Models\User;
use App\Models\Donation;
use App\Models\VolunteerAssignment;
use App\Models\Beneficiary;

class AchievementUsageExamples
{
    protected $achievementService;

    public function __construct()
    {
        $this->achievementService = new EnhancedAchievementService();
    }

    // ==================== BASIC USAGE ====================

    /**
     * Example 1: Check achievements when a donation is completed
     */
    public function completeDonation(Donation $donation)
    {
        // Complete the donation
        $donation->update(['status' => 'completed']);

        // Check for achievements
        $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
            $donation->user,
            'donation_completed',
            [
                'amount' => $donation->amount,
                'type' => $donation->type,
                'currency' => $donation->currency,
                'created_at' => $donation->created_at,
                'donation_id' => $donation->id
            ]
        );

        // Notify user of new achievements
        foreach ($awardedAchievements as $achievement) {
            $this->notifyUserOfAchievement($donation->user, $achievement);
        }

        return $awardedAchievements;
    }

    /**
     * Example 2: Check achievements when a volunteer assignment is completed
     */
    public function completeVolunteerAssignment(VolunteerAssignment $assignment)
    {
        // Complete the assignment
        $assignment->update(['status' => 'completed']);

        // Check for achievements
        $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
            $assignment->volunteer,
            'volunteer_assignment_completed',
            [
                'assignment_id' => $assignment->id,
                'beneficiary_id' => $assignment->beneficiary_id,
                'hours_worked' => $assignment->hours_worked,
                'completed_at' => now()
            ]
        );

        return $awardedAchievements;
    }

    /**
     * Example 3: Check achievements when a beneficiary request is completed
     */
    public function completeBeneficiaryRequest(Beneficiary $beneficiary)
    {
        // Complete the request
        $beneficiary->update(['status' => 'completed']);

        // Check for achievements for all volunteers who helped
        $volunteers = $beneficiary->volunteerAssignments()
            ->where('status', 'completed')
            ->with('volunteer')
            ->get();

        foreach ($volunteers as $assignment) {
            $this->achievementService->checkAndAwardAchievements(
                $assignment->volunteer,
                'beneficiary_request_completed',
                [
                    'beneficiary_id' => $beneficiary->id,
                    'assignment_id' => $assignment->id,
                    'completed_at' => now()
                ]
            );
        }
    }

    // ==================== ACHIEVEMENT CREATION EXAMPLES ====================

    /**
     * Example 4: Create a monetary donation achievement
     */
    public function createMonetaryDonationAchievement()
    {
        return Achievement::create([
            'name' => 'Generous Donor',
            'description' => 'Donated a total of ₹10,000 or more',
            'icon' => 'achievements/icons/generous-donor.png',
            'type' => 'donation',
            'category' => 'monetary',
            'rarity' => 'uncommon',
            'points' => 50,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'total_donation_amount',
                'min_total' => 10000,
                'donation_type' => 'monetary'
            ],
            'is_active' => true,
            'is_repeatable' => true,
            'max_earnings' => 1
        ]);
    }

    /**
     * Example 5: Create a donation count achievement
     */
    public function createDonationCountAchievement()
    {
        return Achievement::create([
            'name' => 'Regular Supporter',
            'description' => 'Completed 5 donations',
            'icon' => 'achievements/icons/regular-supporter.png',
            'type' => 'donation',
            'category' => 'milestone',
            'rarity' => 'uncommon',
            'points' => 40,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'donation_count',
                'count' => 5
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 6: Create a donation type count achievement
     */
    public function createDonationTypeCountAchievement()
    {
        return Achievement::create([
            'name' => 'Material Helper',
            'description' => 'Made 3 materialistic donations',
            'icon' => 'achievements/icons/material-helper.png',
            'type' => 'donation',
            'category' => 'materialistic',
            'rarity' => 'uncommon',
            'points' => 30,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'donation_type_count',
                'donation_type' => 'materialistic',
                'count' => 3
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 7: Create a volunteer completion achievement
     */
    public function createVolunteerCompletionAchievement()
    {
        return Achievement::create([
            'name' => 'Active Volunteer',
            'description' => 'Completed 5 volunteer assignments',
            'icon' => 'achievements/icons/active-volunteer.png',
            'type' => 'volunteer',
            'category' => 'completion',
            'rarity' => 'uncommon',
            'points' => 60,
            'criteria' => [
                'event_type' => 'volunteer_assignment_completed',
                'type' => 'volunteer_completion',
                'count' => 5
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 8: Create a beneficiary help achievement
     */
    public function createBeneficiaryHelpAchievement()
    {
        return Achievement::create([
            'name' => 'Helper of Many',
            'description' => 'Helped 10 or more beneficiaries',
            'icon' => 'achievements/icons/helper-many.png',
            'type' => 'volunteer',
            'category' => 'beneficiary',
            'rarity' => 'rare',
            'points' => 100,
            'criteria' => [
                'event_type' => 'beneficiary_request_completed',
                'type' => 'beneficiary_help',
                'count' => 10
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 9: Create a volunteer hours achievement
     */
    public function createVolunteerHoursAchievement()
    {
        return Achievement::create([
            'name' => 'Dedicated Volunteer',
            'description' => 'Completed 100 volunteer hours',
            'icon' => 'achievements/icons/dedicated-volunteer.png',
            'type' => 'volunteer',
            'category' => 'completion',
            'rarity' => 'rare',
            'points' => 120,
            'criteria' => [
                'event_type' => 'volunteer_assignment_completed',
                'type' => 'volunteer_hours',
                'min_hours' => 100
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 10: Create a milestone achievement
     */
    public function createMilestoneAchievement()
    {
        return Achievement::create([
            'name' => 'First Steps',
            'description' => 'Made your first donation',
            'icon' => 'achievements/icons/first-donation.png',
            'type' => 'donation',
            'category' => 'milestone',
            'rarity' => 'common',
            'points' => 10,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'milestone',
                'milestone_type' => 'first_donation'
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 11: Create a profile completion achievement
     */
    public function createProfileCompletionAchievement()
    {
        return Achievement::create([
            'name' => 'Complete Profile',
            'description' => 'Completed all essential profile details',
            'icon' => 'achievements/icons/complete-profile.png',
            'type' => 'general',
            'category' => 'profile',
            'rarity' => 'common',
            'points' => 20,
            'criteria' => [
                'event_type' => 'profile_updated',
                'type' => 'profile_completion'
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 12: Create a special event achievement
     */
    public function createSpecialEventAchievement()
    {
        return Achievement::create([
            'name' => 'Birthday Giver',
            'description' => 'Made a donation on your birthday',
            'icon' => 'achievements/icons/birthday-giver.png',
            'type' => 'general',
            'category' => 'special',
            'rarity' => 'uncommon',
            'points' => 25,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'special',
                'special_type' => 'birthday_donation'
            ],
            'is_active' => true,
            'is_repeatable' => true
        ]);
    }

    /**
     * Example 13: Create a time-based achievement
     */
    public function createTimeBasedAchievement()
    {
        return Achievement::create([
            'name' => 'Monthly Donor',
            'description' => 'Made donations for 6 consecutive months',
            'icon' => 'achievements/icons/monthly-donor.png',
            'type' => 'donation',
            'category' => 'time_based',
            'rarity' => 'rare',
            'points' => 80,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'time_based',
                'time_type' => 'monthly',
                'months' => 6
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 14: Create a streak achievement
     */
    public function createStreakAchievement()
    {
        return Achievement::create([
            'name' => 'Consistent Giver',
            'description' => 'Made donations for 7 consecutive days',
            'icon' => 'achievements/icons/consistent-giver.png',
            'type' => 'donation',
            'category' => 'streak',
            'rarity' => 'rare',
            'points' => 75,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'streak',
                'streak_type' => 'donation',
                'days' => 7
            ],
            'is_active' => true,
            'is_repeatable' => true
        ]);
    }

    /**
     * Example 15: Create a mixed donation types achievement
     */
    public function createMixedDonationTypesAchievement()
    {
        return Achievement::create([
            'name' => 'Versatile Giver',
            'description' => 'Made at least one donation of each type (monetary, materialistic, service)',
            'icon' => 'achievements/icons/versatile-giver.png',
            'type' => 'donation',
            'category' => 'mixed',
            'rarity' => 'rare',
            'points' => 75,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'mixed_donation_types',
                'monetary' => 1,
                'materialistic' => 1,
                'service' => 1
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 16: Create a donation category count achievement
     */
    public function createDonationCategoryCountAchievement()
    {
        return Achievement::create([
            'name' => 'Clothing Donor',
            'description' => 'Donated clothing items 5 times',
            'icon' => 'achievements/icons/clothing-donor.png',
            'type' => 'donation',
            'category' => 'materialistic',
            'rarity' => 'uncommon',
            'points' => 35,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'donation_category_count',
                'donation_type' => 'materialistic',
                'category' => 'clothing',
                'count' => 5
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    /**
     * Example 17: Create a service hours achievement
     */
    public function createServiceHoursAchievement()
    {
        return Achievement::create([
            'name' => 'Service Provider',
            'description' => 'Completed 50 hours of service donations',
            'icon' => 'achievements/icons/service-provider.png',
            'type' => 'donation',
            'category' => 'service',
            'rarity' => 'uncommon',
            'points' => 45,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'service_hours',
                'min_hours' => 50,
                'donation_type' => 'service'
            ],
            'is_active' => true,
            'is_repeatable' => false
        ]);
    }

    // ==================== PROGRESS TRACKING EXAMPLES ====================

    /**
     * Example 18: Get achievement progress for a user
     */
    public function getUserAchievementProgress(User $user, Achievement $achievement)
    {
        return $this->achievementService->getAchievementProgress($user, $achievement);
    }

    /**
     * Example 19: Get all available achievements with progress
     */
    public function getAvailableAchievementsWithProgress(User $user)
    {
        $earnedAchievementIds = $user->achievements()->pluck('achievement_id')->toArray();
        $availableAchievements = Achievement::active()
            ->available()
            ->whereNotIn('id', $earnedAchievementIds)
            ->get();

        $achievementsWithProgress = [];
        foreach ($availableAchievements as $achievement) {
            $progress = $this->achievementService->getAchievementProgress($user, $achievement);
            $achievementsWithProgress[] = [
                'achievement' => $achievement,
                'progress' => $progress
            ];
        }

        return $achievementsWithProgress;
    }

    // ==================== INTEGRATION EXAMPLES ====================

    /**
     * Example 20: Integration with donation completion event
     */
    public function handleDonationCompleted($donation)
    {
        $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
            $donation->user,
            'donation_completed',
            [
                'amount' => $donation->amount,
                'type' => $donation->type,
                'currency' => $donation->currency,
                'created_at' => $donation->created_at,
                'donation_id' => $donation->id
            ]
        );

        // Send notifications
        foreach ($awardedAchievements as $achievement) {
            $this->sendAchievementNotification($donation->user, $achievement);
        }

        // Update user's achievement points
        $this->updateUserAchievementPoints($donation->user);

        return $awardedAchievements;
    }

    /**
     * Example 21: Integration with volunteer assignment completion
     */
    public function handleVolunteerAssignmentCompleted($assignment)
    {
        $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
            $assignment->volunteer,
            'volunteer_assignment_completed',
            [
                'assignment_id' => $assignment->id,
                'beneficiary_id' => $assignment->beneficiary_id,
                'hours_worked' => $assignment->hours_worked,
                'completed_at' => now()
            ]
        );

        // Send notifications
        foreach ($awardedAchievements as $achievement) {
            $this->sendAchievementNotification($assignment->volunteer, $achievement);
        }

        return $awardedAchievements;
    }

    /**
     * Example 22: Integration with profile update
     */
    public function handleProfileUpdated(User $user)
    {
        $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
            $user,
            'profile_updated',
            [
                'updated_at' => now(),
                'profile_completion' => $this->calculateProfileCompletion($user)
            ]
        );

        return $awardedAchievements;
    }

    /**
     * Example 23: Integration with user registration
     */
    public function handleUserRegistered(User $user)
    {
        $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
            $user,
            'user_registered',
            [
                'registration_date' => $user->created_at,
                'referral_code' => request('referral_code')
            ]
        );

        return $awardedAchievements;
    }

    // ==================== BATCH PROCESSING EXAMPLES ====================

    /**
     * Example 24: Batch check achievements for all users
     */
    public function batchCheckAchievements()
    {
        $users = User::all();
        $totalAwarded = 0;

        foreach ($users as $user) {
            // Check for time-based achievements
            $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
                $user,
                'daily_check',
                ['check_date' => now()]
            );

            $totalAwarded += count($awardedAchievements);
        }

        return $totalAwarded;
    }

    /**
     * Example 25: Batch check achievements for specific event type
     */
    public function batchCheckAchievementsForEvent($eventType, $eventData = [])
    {
        $users = User::all();
        $totalAwarded = 0;

        foreach ($users as $user) {
            $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
                $user,
                $eventType,
                $eventData
            );

            $totalAwarded += count($awardedAchievements);
        }

        return $totalAwarded;
    }

    // ==================== ANALYTICS EXAMPLES ====================

    /**
     * Example 26: Get user achievement statistics
     */
    public function getUserAchievementStats(User $user)
    {
        return [
            'total_earned' => $user->achievements()->count(),
            'total_points' => $user->total_achievement_points,
            'by_type' => $user->achievements()
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'by_rarity' => $user->achievements()
                ->selectRaw('rarity, count(*) as count')
                ->groupBy('rarity')
                ->pluck('count', 'rarity'),
            'recent_achievements' => $user->recentAchievements(30),
            'next_achievements' => $this->getNextAchievements($user)
        ];
    }

    /**
     * Example 27: Get platform achievement analytics
     */
    public function getPlatformAchievementStats()
    {
        return [
            'total_achievements' => Achievement::active()->count(),
            'total_awarded' => \App\Models\UserAchievement::count(),
            'most_earned' => \App\Models\UserAchievement::with('achievement')
                ->selectRaw('achievement_id, count(*) as count')
                ->groupBy('achievement_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'user_leaderboard' => User::withCount('achievements')
                ->orderBy('achievements_count', 'desc')
                ->limit(10)
                ->get()
        ];
    }

    // ==================== HELPER METHODS ====================

    /**
     * Send achievement notification to user
     */
    protected function sendAchievementNotification(User $user, $achievement)
    {
        // Implement your notification logic here
        // This could be email, push notification, in-app notification, etc.

        // Example: Send email notification
        // Mail::to($user->email)->send(new AchievementEarnedMail($achievement));

        // Example: Create in-app notification
        // $user->notifications()->create([
        //     'type' => 'achievement_earned',
        //     'data' => ['achievement_id' => $achievement->id],
        //     'read_at' => null
        // ]);
    }

    /**
     * Update user's total achievement points
     */
    protected function updateUserAchievementPoints(User $user)
    {
        $totalPoints = $user->achievements()->sum('points');
        $user->update(['total_achievement_points' => $totalPoints]);
    }

    /**
     * Calculate profile completion percentage
     */
    protected function calculateProfileCompletion(User $user)
    {
        $requiredFields = ['name', 'email', 'phone', 'address', 'city_id'];
        $completedFields = 0;

        foreach ($requiredFields as $field) {
            if (!empty($user->$field)) {
                $completedFields++;
            }
        }

        return ($completedFields / count($requiredFields)) * 100;
    }

    /**
     * Get next achievements for a user
     */
    protected function getNextAchievements(User $user)
    {
        $earnedAchievementIds = $user->achievements()->pluck('achievement_id')->toArray();

        return Achievement::active()
            ->available()
            ->whereNotIn('id', $earnedAchievementIds)
            ->orderBy('points', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Notify user of achievement
     */
    protected function notifyUserOfAchievement(User $user, $achievement)
    {
        // Implement notification logic
        $this->sendAchievementNotification($user, $achievement);
    }
}

// ==================== EVENT LISTENER EXAMPLES ====================

/**
 * Example 28: Event listener for donation completion
 */
class DonationCompletedListener
{
    protected $achievementService;

    public function __construct(EnhancedAchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle($event)
    {
        $donation = $event->donation;

        $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
            $donation->user,
            'donation_completed',
            [
                'amount' => $donation->amount,
                'type' => $donation->type,
                'currency' => $donation->currency,
                'created_at' => $donation->created_at,
                'donation_id' => $donation->id
            ]
        );

        // Dispatch achievement earned events
        foreach ($awardedAchievements as $achievement) {
            event(new AchievementEarned($donation->user, $achievement));
        }
    }
}

/**
 * Example 29: Event listener for volunteer assignment completion
 */
class VolunteerAssignmentCompletedListener
{
    protected $achievementService;

    public function __construct(EnhancedAchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle($event)
    {
        $assignment = $event->assignment;

        $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
            $assignment->volunteer,
            'volunteer_assignment_completed',
            [
                'assignment_id' => $assignment->id,
                'beneficiary_id' => $assignment->beneficiary_id,
                'hours_worked' => $assignment->hours_worked,
                'completed_at' => now()
            ]
        );

        // Dispatch achievement earned events
        foreach ($awardedAchievements as $achievement) {
            event(new AchievementEarned($assignment->volunteer, $achievement));
        }
    }
}

// ==================== COMMAND EXAMPLES ====================

/**
 * Example 30: Artisan command for batch achievement checking
 */
class CheckAchievementsCommand extends Command
{
    protected $signature = 'achievements:check {--event=} {--user=}';
    protected $description = 'Check and award achievements for users';

    protected $achievementService;

    public function __construct(EnhancedAchievementService $achievementService)
    {
        parent::__construct();
        $this->achievementService = $achievementService;
    }

    public function handle()
    {
        $eventType = $this->option('event');
        $userId = $this->option('user');

        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error('User not found');
                return;
            }

            $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
                $user,
                $eventType ?? 'daily_check',
                ['check_date' => now()]
            );

            $this->info("Awarded " . count($awardedAchievements) . " achievements to user {$user->name}");
        } else {
            $users = User::all();
            $totalAwarded = 0;

            foreach ($users as $user) {
                $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
                    $user,
                    $eventType ?? 'daily_check',
                    ['check_date' => now()]
                );

                $totalAwarded += count($awardedAchievements);
            }

            $this->info("Awarded {$totalAwarded} achievements to all users");
        }
    }
}

// ==================== MIDDLEWARE EXAMPLES ====================

/**
 * Example 31: Middleware to check achievements on user login
 */
class CheckAchievementsOnLogin
{
    protected $achievementService;

    public function __construct(EnhancedAchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check()) {
            // Check for login streak achievements
            $this->achievementService->checkAndAwardAchievements(
                auth()->user(),
                'user_login',
                ['login_date' => now()]
            );
        }

        return $response;
    }
}

// ==================== SCHEDULED TASK EXAMPLES ====================

/**
 * Example 32: Scheduled task for daily achievement checking
 */
class DailyAchievementCheck
{
    protected $achievementService;

    public function __construct(EnhancedAchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle()
    {
        $users = User::all();
        $totalAwarded = 0;

        foreach ($users as $user) {
            // Check for time-based achievements
            $awardedAchievements = $this->achievementService->checkAndAwardAchievements(
                $user,
                'daily_check',
                ['check_date' => now()]
            );

            $totalAwarded += count($awardedAchievements);
        }

        \Log::info("Daily achievement check completed. Awarded {$totalAwarded} achievements.");
    }
}

// ==================== API EXAMPLES ====================

/**
 * Example 33: API endpoint for getting user achievements
 */
class AchievementController extends Controller
{
    protected $achievementService;

    public function __construct(EnhancedAchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function getUserAchievements(User $user)
    {
        $earnedAchievements = $user->achievements()->with('achievement')->get();
        $availableAchievements = Achievement::active()
            ->available()
            ->whereNotIn('id', $earnedAchievements->pluck('achievement_id'))
            ->get();

        $achievementsWithProgress = [];
        foreach ($availableAchievements as $achievement) {
            $progress = $this->achievementService->getAchievementProgress($user, $achievement);
            $achievementsWithProgress[] = [
                'achievement' => $achievement,
                'progress' => $progress
            ];
        }

        return response()->json([
            'earned_achievements' => $earnedAchievements,
            'available_achievements' => $achievementsWithProgress,
            'stats' => [
                'total_earned' => $earnedAchievements->count(),
                'total_points' => $user->total_achievement_points,
                'total_available' => $availableAchievements->count()
            ]
        ]);
    }

    public function getAchievementProgress(User $user, Achievement $achievement)
    {
        $progress = $this->achievementService->getAchievementProgress($user, $achievement);

        return response()->json([
            'achievement' => $achievement,
            'progress' => $progress
        ]);
    }
}
