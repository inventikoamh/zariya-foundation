<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\Donation;
use App\Models\Beneficiary;
use App\Models\VolunteerAssignment;
use App\Services\EmailNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EnhancedAchievementService
{
    /**
     * Check and award achievements for a given user based on an event.
     *
     * @param User $user
     * @param string $eventType e.g., 'donation_completed', 'volunteer_assignment_completed'
     * @param array $eventData Contextual data for the event
     * @return array An array of awarded UserAchievement instances
     */
    public function checkAndAwardAchievements(User $user, string $eventType, array $eventData = []): array
    {
        $awardedAchievements = [];
        $activeAchievements = Achievement::where('is_active', true)->get();

        foreach ($activeAchievements as $achievement) {
            if ($this->evaluateAchievementCriteria($user, $achievement, $eventType, $eventData)) {
                $awarded = $this->awardAchievement($user, $achievement, $eventData);
                if ($awarded) {
                    $awardedAchievements[] = $awarded;
                }
            }
        }

        return $awardedAchievements;
    }

    /**
     * Evaluate if a user meets the criteria for a specific achievement.
     */
    protected function evaluateAchievementCriteria(User $user, Achievement $achievement, string $eventType, array $eventData): bool
    {
        $criteria = $achievement->criteria;

        if (!isset($criteria['event_type']) || $criteria['event_type'] !== $eventType) {
            return false;
        }

        switch ($criteria['type']) {
            case 'donation_amount':
                return $this->checkDonationAmount($user, $criteria, $eventData);
            case 'donation_count':
                return $this->checkDonationCount($user, $criteria, $eventData);
            case 'donation_type_count':
                return $this->checkDonationTypeCount($user, $criteria, $eventData);
            case 'total_donation_amount':
                return $this->checkTotalDonationAmount($user, $criteria, $eventData);
            case 'volunteer_completion':
                return $this->checkVolunteerCompletion($user, $criteria, $eventData);
            case 'beneficiary_help':
                return $this->checkBeneficiaryHelp($user, $criteria, $eventData);
            case 'volunteer_hours':
                return $this->checkVolunteerHours($user, $criteria, $eventData);
            case 'streak':
                return $this->checkStreak($user, $criteria, $eventData);
            case 'milestone':
                return $this->checkMilestone($user, $criteria, $eventData);
            case 'profile_completion':
                return $this->checkProfileCompletion($user, $criteria, $eventData);
            case 'time_based':
                return $this->checkTimeBased($user, $criteria, $eventData);
            case 'special':
                return $this->checkSpecial($user, $criteria, $eventData);
            case 'engagement':
                return $this->checkEngagement($user, $criteria, $eventData);
            case 'referral':
                return $this->checkReferral($user, $criteria, $eventData);
            case 'service_hours':
                return $this->checkServiceHours($user, $criteria, $eventData);
            case 'donation_category_count':
                return $this->checkDonationCategoryCount($user, $criteria, $eventData);
            case 'mixed_donation_types':
                return $this->checkMixedDonationTypes($user, $criteria, $eventData);
            default:
                return false;
        }
    }

    /**
     * Award an achievement to a user.
     */
    protected function awardAchievement(User $user, Achievement $achievement, array $metadata = []): ?UserAchievement
    {
        // Check if already earned and if repeatable
        $existingAchievementCount = UserAchievement::where('user_id', $user->id)
                                                    ->where('achievement_id', $achievement->id)
                                                    ->count();

        if (!$achievement->is_repeatable && $existingAchievementCount > 0) {
            return null; // Already earned and not repeatable
        }

        if ($achievement->is_repeatable && $achievement->max_earnings !== null && $existingAchievementCount >= $achievement->max_earnings) {
            return null; // Max earnings reached
        }

        try {
            DB::beginTransaction();

            $userAchievement = UserAchievement::create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'earned_at' => now(),
                'metadata' => $metadata,
                'is_notified' => false,
            ]);

            // Send achievement earned email notification
            try {
                $emailService = app(EmailNotificationService::class);
                $emailService->sendAchievementEarned($user, $achievement);

                // Mark as notified
                $userAchievement->update(['is_notified' => true]);
            } catch (\Exception $e) {
                Log::error('Failed to send achievement earned email', [
                    'user_id' => $user->id,
                    'achievement_id' => $achievement->id,
                    'error' => $e->getMessage()
                ]);
            }

            DB::commit();
            return $userAchievement;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to award achievement', [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get progress towards a specific achievement for a user.
     */
    public function getAchievementProgress(User $user, Achievement $achievement): array
    {
        $criteria = $achievement->criteria;

        switch ($criteria['type']) {
            case 'donation_amount':
                return $this->getDonationAmountProgress($user, $criteria);
            case 'donation_count':
                return $this->getDonationCountProgress($user, $criteria);
            case 'donation_type_count':
                return $this->getDonationTypeCountProgress($user, $criteria);
            case 'total_donation_amount':
                return $this->getTotalDonationAmountProgress($user, $criteria);
            case 'volunteer_completion':
                return $this->getVolunteerCompletionProgress($user, $criteria);
            case 'beneficiary_help':
                return $this->getBeneficiaryHelpProgress($user, $criteria);
            case 'volunteer_hours':
                return $this->getVolunteerHoursProgress($user, $criteria);
            case 'streak':
                return $this->getStreakProgress($user, $criteria);
            case 'milestone':
                return $this->getMilestoneProgress($user, $criteria);
            case 'profile_completion':
                return $this->getProfileCompletionProgress($user, $criteria);
            case 'time_based':
                return $this->getTimeBasedProgress($user, $criteria);
            case 'special':
                return $this->getSpecialProgress($user, $criteria);
            case 'engagement':
                return $this->getEngagementProgress($user, $criteria);
            case 'referral':
                return $this->getReferralProgress($user, $criteria);
            case 'service_hours':
                return $this->getServiceHoursProgress($user, $criteria);
            case 'donation_category_count':
                return $this->getDonationCategoryCountProgress($user, $criteria);
            case 'mixed_donation_types':
                return $this->getMixedDonationTypesProgress($user, $criteria);
            default:
                return ['current' => 0, 'target' => 1, 'percentage' => 0, 'description' => 'Unknown criteria'];
        }
    }

    // ==================== CRITERIA CHECKERS ====================

    protected function checkDonationAmount(User $user, array $criteria, array $eventData): bool
    {
        if ($eventData['type'] !== 'monetary' || !isset($eventData['amount'])) {
            return false;
        }
        return $eventData['amount'] >= ($criteria['min_amount'] ?? 0);
    }

    protected function checkDonationCount(User $user, array $criteria, array $eventData): bool
    {
        $currentDonationCount = $user->donations()->where('status', 'completed')->count();
        return $currentDonationCount >= ($criteria['count'] ?? 0);
    }

    protected function checkDonationTypeCount(User $user, array $criteria, array $eventData): bool
    {
        if (!isset($criteria['donation_type']) || !isset($criteria['count'])) {
            return false;
        }
        $count = $user->donations()->where('type', $criteria['donation_type'])->where('status', 'completed')->count();
        return $count >= $criteria['count'];
    }

    protected function checkTotalDonationAmount(User $user, array $criteria, array $eventData): bool
    {
        $totalAmount = $user->donations()
            ->where('type', 'monetary')
            ->where('status', 'completed')
            ->sum('amount');
        return $totalAmount >= ($criteria['min_total'] ?? 0);
    }

    protected function checkVolunteerCompletion(User $user, array $criteria, array $eventData): bool
    {
        $completedAssignments = $user->volunteerAssignments()->where('status', 'completed')->count();
        return $completedAssignments >= ($criteria['count'] ?? 0);
    }

    protected function checkBeneficiaryHelp(User $user, array $criteria, array $eventData): bool
    {
        $beneficiariesHelped = $user->volunteerAssignments()
            ->where('status', 'completed')
            ->distinct('beneficiary_id')
            ->count();
        return $beneficiariesHelped >= ($criteria['count'] ?? 0);
    }

    protected function checkVolunteerHours(User $user, array $criteria, array $eventData): bool
    {
        $totalHours = $user->volunteerAssignments()
            ->where('status', 'completed')
            ->sum('hours_worked');
        return $totalHours >= ($criteria['min_hours'] ?? 0);
    }

    protected function checkStreak(User $user, array $criteria, array $eventData): bool
    {
        $streakType = $criteria['streak_type'] ?? 'donation';
        $requiredDays = $criteria['days'] ?? 7;

        switch ($streakType) {
            case 'donation':
                return $this->getDonationStreak($user) >= $requiredDays;
            case 'volunteer':
                return $this->getVolunteerStreak($user) >= $requiredDays;
            case 'login':
                return $this->getLoginStreak($user) >= $requiredDays;
            default:
                return false;
        }
    }

    protected function checkMilestone(User $user, array $criteria, array $eventData): bool
    {
        $milestoneType = $criteria['milestone_type'] ?? null;

        switch ($milestoneType) {
            case 'first_donation':
                return $user->donations()->where('status', 'completed')->count() === 1;
            case 'first_volunteer':
                return $user->volunteerAssignments()->where('status', 'completed')->count() === 1;
            case 'profile_complete':
                return $this->isProfileComplete($user);
            case 'first_assignment':
                return $user->volunteerAssignments()->where('status', 'completed')->count() === 1;
            default:
                return false;
        }
    }

    protected function checkProfileCompletion(User $user, array $criteria, array $eventData): bool
    {
        return $this->isProfileComplete($user);
    }

    protected function checkTimeBased(User $user, array $criteria, array $eventData): bool
    {
        $timeType = $criteria['time_type'] ?? null;

        switch ($timeType) {
            case 'monthly':
                return $this->checkMonthlyDonation($user, $criteria);
            case 'yearly':
                return $this->checkYearlyDonation($user, $criteria);
            case 'early_adopter':
                return $this->checkEarlyAdopter($user, $criteria);
            default:
                return false;
        }
    }

    protected function checkSpecial(User $user, array $criteria, array $eventData): bool
    {
        $specialType = $criteria['special_type'] ?? null;

        switch ($specialType) {
            case 'birthday_donation':
                return $this->isBirthdayDonation($user, $eventData);
            case 'holiday_donation':
                return $this->isHolidayDonation($eventData);
            case 'single_large_donation':
                return $this->isSingleLargeDonation($eventData, $criteria);
            case 'emergency_response':
                return $this->isEmergencyResponse($eventData, $criteria);
            default:
                return false;
        }
    }

    protected function checkEngagement(User $user, array $criteria, array $eventData): bool
    {
        $engagementType = $criteria['engagement_type'] ?? null;

        switch ($engagementType) {
            case 'social_share':
                return $this->checkSocialShare($user, $criteria);
            case 'profile_views':
                return $this->checkProfileViews($user, $criteria);
            default:
                return false;
        }
    }

    protected function checkReferral(User $user, array $criteria, array $eventData): bool
    {
        $referralCount = $user->referrals()->count();
        return $referralCount >= ($criteria['count'] ?? 0);
    }

    protected function checkServiceHours(User $user, array $criteria, array $eventData): bool
    {
        $totalServiceHours = $user->donations()
            ->where('type', 'service')
            ->where('status', 'completed')
            ->sum('service_hours');
        return $totalServiceHours >= ($criteria['min_hours'] ?? 0);
    }

    protected function checkDonationCategoryCount(User $user, array $criteria, array $eventData): bool
    {
        if (!isset($criteria['category']) || !isset($criteria['count'])) {
            return false;
        }

        $count = $user->donations()
            ->where('type', 'materialistic')
            ->where('category', $criteria['category'])
            ->where('status', 'completed')
            ->count();
        return $count >= $criteria['count'];
    }

    protected function checkMixedDonationTypes(User $user, array $criteria, array $eventData): bool
    {
        $hasMonetary = $user->donations()->where('type', 'monetary')->where('status', 'completed')->exists();
        $hasMaterialistic = $user->donations()->where('type', 'materialistic')->where('status', 'completed')->exists();
        $hasService = $user->donations()->where('type', 'service')->where('status', 'completed')->exists();

        return $hasMonetary && $hasMaterialistic && $hasService;
    }

    // ==================== PROGRESS CALCULATORS ====================

    protected function getDonationAmountProgress(User $user, array $criteria): array
    {
        $currentAmount = $user->donations()
            ->where('type', 'monetary')
            ->where('status', 'completed')
            ->sum('amount');
        $targetAmount = $criteria['min_amount'] ?? 0;

        return [
            'current' => $currentAmount,
            'target' => $targetAmount,
            'percentage' => $targetAmount > 0 ? min(100, ($currentAmount / $targetAmount) * 100) : 0,
            'description' => "₹{$currentAmount} of ₹{$targetAmount} donated"
        ];
    }

    protected function getDonationCountProgress(User $user, array $criteria): array
    {
        $currentCount = $user->donations()->where('status', 'completed')->count();
        $targetCount = $criteria['count'] ?? 0;

        return [
            'current' => $currentCount,
            'target' => $targetCount,
            'percentage' => $targetCount > 0 ? min(100, ($currentCount / $targetCount) * 100) : 0,
            'description' => "{$currentCount} of {$targetCount} donations completed"
        ];
    }

    protected function getDonationTypeCountProgress(User $user, array $criteria): array
    {
        $donationType = $criteria['donation_type'] ?? 'monetary';
        $currentCount = $user->donations()
            ->where('type', $donationType)
            ->where('status', 'completed')
            ->count();
        $targetCount = $criteria['count'] ?? 0;

        return [
            'current' => $currentCount,
            'target' => $targetCount,
            'percentage' => $targetCount > 0 ? min(100, ($currentCount / $targetCount) * 100) : 0,
            'description' => "{$currentCount} of {$targetCount} {$donationType} donations completed"
        ];
    }

    protected function getTotalDonationAmountProgress(User $user, array $criteria): array
    {
        $currentAmount = $user->donations()
            ->where('type', 'monetary')
            ->where('status', 'completed')
            ->sum('amount');
        $targetAmount = $criteria['min_total'] ?? 0;

        return [
            'current' => $currentAmount,
            'target' => $targetAmount,
            'percentage' => $targetAmount > 0 ? min(100, ($currentAmount / $targetAmount) * 100) : 0,
            'description' => "₹{$currentAmount} of ₹{$targetAmount} total donated"
        ];
    }

    protected function getVolunteerCompletionProgress(User $user, array $criteria): array
    {
        $currentCount = $user->volunteerAssignments()->where('status', 'completed')->count();
        $targetCount = $criteria['count'] ?? 0;

        return [
            'current' => $currentCount,
            'target' => $targetCount,
            'percentage' => $targetCount > 0 ? min(100, ($currentCount / $targetCount) * 100) : 0,
            'description' => "{$currentCount} of {$targetCount} volunteer assignments completed"
        ];
    }

    protected function getBeneficiaryHelpProgress(User $user, array $criteria): array
    {
        $currentCount = $user->volunteerAssignments()
            ->where('status', 'completed')
            ->distinct('beneficiary_id')
            ->count();
        $targetCount = $criteria['count'] ?? 0;

        return [
            'current' => $currentCount,
            'target' => $targetCount,
            'percentage' => $targetCount > 0 ? min(100, ($currentCount / $targetCount) * 100) : 0,
            'description' => "Helped {$currentCount} of {$targetCount} beneficiaries"
        ];
    }

    protected function getVolunteerHoursProgress(User $user, array $criteria): array
    {
        $currentHours = $user->volunteerAssignments()
            ->where('status', 'completed')
            ->sum('hours_worked');
        $targetHours = $criteria['min_hours'] ?? 0;

        return [
            'current' => $currentHours,
            'target' => $targetHours,
            'percentage' => $targetHours > 0 ? min(100, ($currentHours / $targetHours) * 100) : 0,
            'description' => "{$currentHours} of {$targetHours} volunteer hours completed"
        ];
    }

    protected function getStreakProgress(User $user, array $criteria): array
    {
        $streakType = $criteria['streak_type'] ?? 'donation';
        $requiredDays = $criteria['days'] ?? 7;

        switch ($streakType) {
            case 'donation':
                $currentStreak = $this->getDonationStreak($user);
                break;
            case 'volunteer':
                $currentStreak = $this->getVolunteerStreak($user);
                break;
            case 'login':
                $currentStreak = $this->getLoginStreak($user);
                break;
            default:
                $currentStreak = 0;
        }

        return [
            'current' => $currentStreak,
            'target' => $requiredDays,
            'percentage' => $requiredDays > 0 ? min(100, ($currentStreak / $requiredDays) * 100) : 0,
            'description' => "{$currentStreak} of {$requiredDays} day {$streakType} streak"
        ];
    }

    protected function getMilestoneProgress(User $user, array $criteria): array
    {
        $milestoneType = $criteria['milestone_type'] ?? null;

        switch ($milestoneType) {
            case 'first_donation':
                $hasDonation = $user->donations()->where('status', 'completed')->exists();
                return [
                    'current' => $hasDonation ? 1 : 0,
                    'target' => 1,
                    'percentage' => $hasDonation ? 100 : 0,
                    'description' => $hasDonation ? 'First donation completed!' : 'Make your first donation'
                ];
            case 'first_volunteer':
                $hasVolunteer = $user->volunteerAssignments()->where('status', 'completed')->exists();
                return [
                    'current' => $hasVolunteer ? 1 : 0,
                    'target' => 1,
                    'percentage' => $hasVolunteer ? 100 : 0,
                    'description' => $hasVolunteer ? 'First volunteer assignment completed!' : 'Complete your first volunteer assignment'
                ];
            case 'profile_complete':
                $isComplete = $this->isProfileComplete($user);
                return [
                    'current' => $isComplete ? 1 : 0,
                    'target' => 1,
                    'percentage' => $isComplete ? 100 : 0,
                    'description' => $isComplete ? 'Profile is complete!' : 'Complete your profile'
                ];
            default:
                return ['current' => 0, 'target' => 1, 'percentage' => 0, 'description' => 'Unknown milestone'];
        }
    }

    protected function getProfileCompletionProgress(User $user, array $criteria): array
    {
        $isComplete = $this->isProfileComplete($user);
        return [
            'current' => $isComplete ? 1 : 0,
            'target' => 1,
            'percentage' => $isComplete ? 100 : 0,
            'description' => $isComplete ? 'Profile is complete!' : 'Complete your profile'
        ];
    }

    protected function getTimeBasedProgress(User $user, array $criteria): array
    {
        $timeType = $criteria['time_type'] ?? null;

        switch ($timeType) {
            case 'monthly':
                return $this->getMonthlyDonationProgress($user, $criteria);
            case 'yearly':
                return $this->getYearlyDonationProgress($user, $criteria);
            case 'early_adopter':
                return $this->getEarlyAdopterProgress($user, $criteria);
            default:
                return ['current' => 0, 'target' => 1, 'percentage' => 0, 'description' => 'Unknown time-based criteria'];
        }
    }

    protected function getSpecialProgress(User $user, array $criteria): array
    {
        $specialType = $criteria['special_type'] ?? null;

        switch ($specialType) {
            case 'birthday_donation':
                $isBirthday = $this->isBirthdayDonation($user, []);
                return [
                    'current' => $isBirthday ? 1 : 0,
                    'target' => 1,
                    'percentage' => $isBirthday ? 100 : 0,
                    'description' => $isBirthday ? 'It\'s your birthday! Make a donation.' : 'Make a donation on your birthday'
                ];
            case 'holiday_donation':
                $isHoliday = $this->isHolidayDonation([]);
                return [
                    'current' => $isHoliday ? 1 : 0,
                    'target' => 1,
                    'percentage' => $isHoliday ? 100 : 0,
                    'description' => $isHoliday ? 'It\'s a holiday! Make a donation.' : 'Make a donation during a holiday'
                ];
            default:
                return ['current' => 0, 'target' => 1, 'percentage' => 0, 'description' => 'Unknown special criteria'];
        }
    }

    protected function getEngagementProgress(User $user, array $criteria): array
    {
        $engagementType = $criteria['engagement_type'] ?? null;

        switch ($engagementType) {
            case 'social_share':
                $currentShares = $user->socialShares()->count();
                $targetShares = $criteria['count'] ?? 0;
                return [
                    'current' => $currentShares,
                    'target' => $targetShares,
                    'percentage' => $targetShares > 0 ? min(100, ($currentShares / $targetShares) * 100) : 0,
                    'description' => "{$currentShares} of {$targetShares} social shares"
                ];
            default:
                return ['current' => 0, 'target' => 1, 'percentage' => 0, 'description' => 'Unknown engagement criteria'];
        }
    }

    protected function getReferralProgress(User $user, array $criteria): array
    {
        $currentReferrals = $user->referrals()->count();
        $targetReferrals = $criteria['count'] ?? 0;

        return [
            'current' => $currentReferrals,
            'target' => $targetReferrals,
            'percentage' => $targetReferrals > 0 ? min(100, ($currentReferrals / $targetReferrals) * 100) : 0,
            'description' => "{$currentReferrals} of {$targetReferrals} referrals"
        ];
    }

    protected function getServiceHoursProgress(User $user, array $criteria): array
    {
        $currentHours = $user->donations()
            ->where('type', 'service')
            ->where('status', 'completed')
            ->sum('service_hours');
        $targetHours = $criteria['min_hours'] ?? 0;

        return [
            'current' => $currentHours,
            'target' => $targetHours,
            'percentage' => $targetHours > 0 ? min(100, ($currentHours / $targetHours) * 100) : 0,
            'description' => "{$currentHours} of {$targetHours} service hours completed"
        ];
    }

    protected function getDonationCategoryCountProgress(User $user, array $criteria): array
    {
        $category = $criteria['category'] ?? 'general';
        $currentCount = $user->donations()
            ->where('type', 'materialistic')
            ->where('category', $category)
            ->where('status', 'completed')
            ->count();
        $targetCount = $criteria['count'] ?? 0;

        return [
            'current' => $currentCount,
            'target' => $targetCount,
            'percentage' => $targetCount > 0 ? min(100, ($currentCount / $targetCount) * 100) : 0,
            'description' => "{$currentCount} of {$targetCount} {$category} donations"
        ];
    }

    protected function getMixedDonationTypesProgress(User $user, array $criteria): array
    {
        $hasMonetary = $user->donations()->where('type', 'monetary')->where('status', 'completed')->exists();
        $hasMaterialistic = $user->donations()->where('type', 'materialistic')->where('status', 'completed')->exists();
        $hasService = $user->donations()->where('type', 'service')->where('status', 'completed')->exists();

        $completedTypes = ($hasMonetary ? 1 : 0) + ($hasMaterialistic ? 1 : 0) + ($hasService ? 1 : 0);

        return [
            'current' => $completedTypes,
            'target' => 3,
            'percentage' => ($completedTypes / 3) * 100,
            'description' => "{$completedTypes} of 3 donation types completed"
        ];
    }

    // ==================== HELPER METHODS ====================

    protected function isProfileComplete(User $user): bool
    {
        return !empty($user->name) &&
               !empty($user->email) &&
               !empty($user->phone) &&
               !empty($user->address) &&
               !empty($user->city_id);
    }

    protected function isBirthdayDonation(User $user, array $eventData): bool
    {
        if (!$user->date_of_birth) {
            return false;
        }

        $birthday = Carbon::parse($user->date_of_birth);
        $today = Carbon::now();

        return $birthday->month === $today->month && $birthday->day === $today->day;
    }

    protected function isHolidayDonation(array $eventData): bool
    {
        $today = Carbon::now();
        $holidays = [
            '12-25', // Christmas
            '01-01', // New Year
            '10-31', // Halloween
            '11-24', // Thanksgiving
            '12-31', // New Year's Eve
        ];

        $todayFormatted = $today->format('m-d');
        return in_array($todayFormatted, $holidays);
    }

    protected function isSingleLargeDonation(array $eventData, array $criteria): bool
    {
        $amount = $eventData['amount'] ?? 0;
        $threshold = $criteria['min_amount'] ?? 25000;
        return $amount >= $threshold;
    }

    protected function isEmergencyResponse(array $eventData, array $criteria): bool
    {
        $timeframe = $criteria['timeframe'] ?? 24; // hours
        $donationDate = Carbon::parse($eventData['created_at'] ?? now());
        $emergencyStart = Carbon::parse($eventData['emergency_start'] ?? now()->subHours($timeframe));

        return $donationDate->isAfter($emergencyStart);
    }

    protected function checkSocialShare(User $user, array $criteria): bool
    {
        $platform = $criteria['platform'] ?? null;
        $count = $criteria['count'] ?? 0;

        $query = $user->socialShares();
        if ($platform) {
            $query->where('platform', $platform);
        }

        return $query->count() >= $count;
    }

    protected function checkProfileViews(User $user, array $criteria): bool
    {
        $viewCount = $user->profile_views ?? 0;
        return $viewCount >= ($criteria['count'] ?? 0);
    }

    protected function checkMonthlyDonation(User $user, array $criteria): bool
    {
        $requiredMonths = $criteria['months'] ?? 6;
        $monthlyDonations = $user->donations()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths($requiredMonths))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->groupBy('month')
            ->havingRaw('COUNT(*) > 0')
            ->count();

        return $monthlyDonations >= $requiredMonths;
    }

    protected function checkYearlyDonation(User $user, array $criteria): bool
    {
        $requiredYears = $criteria['years'] ?? 2;
        $yearlyDonations = $user->donations()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subYears($requiredYears))
            ->selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->havingRaw('COUNT(*) > 0')
            ->count();

        return $yearlyDonations >= $requiredYears;
    }

    protected function checkEarlyAdopter(User $user, array $criteria): bool
    {
        $daysSinceLaunch = $criteria['days_since_launch'] ?? 30;
        $launchDate = Carbon::parse($criteria['launch_date'] ?? '2024-01-01');
        $userRegistration = $user->created_at;

        return $userRegistration->isBefore($launchDate->addDays($daysSinceLaunch));
    }

    protected function getDonationStreak(User $user): int
    {
        // This would need to be implemented based on your streak tracking logic
        // For now, return a placeholder
        return 0;
    }

    protected function getVolunteerStreak(User $user): int
    {
        // This would need to be implemented based on your streak tracking logic
        // For now, return a placeholder
        return 0;
    }

    protected function getLoginStreak(User $user): int
    {
        // This would need to be implemented based on your streak tracking logic
        // For now, return a placeholder
        return 0;
    }

    protected function getMonthlyDonationProgress(User $user, array $criteria): array
    {
        $requiredMonths = $criteria['months'] ?? 6;
        $monthlyDonations = $user->donations()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths($requiredMonths))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->groupBy('month')
            ->havingRaw('COUNT(*) > 0')
            ->count();

        return [
            'current' => $monthlyDonations,
            'target' => $requiredMonths,
            'percentage' => $requiredMonths > 0 ? min(100, ($monthlyDonations / $requiredMonths) * 100) : 0,
            'description' => "Donated in {$monthlyDonations} of {$requiredMonths} months"
        ];
    }

    protected function getYearlyDonationProgress(User $user, array $criteria): array
    {
        $requiredYears = $criteria['years'] ?? 2;
        $yearlyDonations = $user->donations()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subYears($requiredYears))
            ->selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->havingRaw('COUNT(*) > 0')
            ->count();

        return [
            'current' => $yearlyDonations,
            'target' => $requiredYears,
            'percentage' => $requiredYears > 0 ? min(100, ($yearlyDonations / $requiredYears) * 100) : 0,
            'description' => "Donated in {$yearlyDonations} of {$requiredYears} years"
        ];
    }

    protected function getEarlyAdopterProgress(User $user, array $criteria): array
    {
        $daysSinceLaunch = $criteria['days_since_launch'] ?? 30;
        $launchDate = Carbon::parse($criteria['launch_date'] ?? '2024-01-01');
        $userRegistration = $user->created_at;
        $isEarlyAdopter = $userRegistration->isBefore($launchDate->addDays($daysSinceLaunch));

        return [
            'current' => $isEarlyAdopter ? 1 : 0,
            'target' => 1,
            'percentage' => $isEarlyAdopter ? 100 : 0,
            'description' => $isEarlyAdopter ? 'You are an early adopter!' : 'Register within 30 days of launch'
        ];
    }
}
