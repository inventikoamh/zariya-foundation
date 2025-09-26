<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\Donation;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    /**
     * Check and award achievements for a user based on their activities.
     */
    public function checkAndAwardAchievements(User $user, $triggerType = null, $triggerData = [])
    {
        $achievements = Achievement::active()
            ->available()
            ->where('type', $triggerType)
            ->get();

        $awardedAchievements = [];

        foreach ($achievements as $achievement) {
            if ($this->checkAchievementCriteria($user, $achievement, $triggerData)) {
                if ($this->awardAchievement($user, $achievement, $triggerData)) {
                    $awardedAchievements[] = $achievement;
                }
            }
        }

        return $awardedAchievements;
    }

    /**
     * Check if a user meets the criteria for a specific achievement.
     */
    public function checkAchievementCriteria(User $user, Achievement $achievement, $triggerData = [])
    {
        if (!$achievement->canBeEarnedBy($user)) {
            return false;
        }

        $criteria = $achievement->criteria;
        $type = $criteria['type'] ?? null;

        switch ($type) {
            case 'donation_amount':
                return $this->checkDonationAmountCriteria($user, $criteria);

            case 'donation_count':
                return $this->checkDonationCountCriteria($user, $criteria);

            case 'donation_type_count':
                return $this->checkDonationTypeCountCriteria($user, $criteria);

            case 'volunteer_completion':
                return $this->checkVolunteerCompletionCriteria($user, $criteria);

            case 'beneficiary_help':
                return $this->checkBeneficiaryHelpCriteria($user, $criteria);

            case 'streak':
                return $this->checkStreakCriteria($user, $criteria);

            case 'milestone':
                return $this->checkMilestoneCriteria($user, $criteria);

            case 'special':
                return $this->checkSpecialCriteria($user, $criteria, $triggerData);

            default:
                return false;
        }
    }

    /**
     * Award an achievement to a user.
     */
    public function awardAchievement(User $user, Achievement $achievement, $metadata = [])
    {
        try {
            DB::beginTransaction();

            $userAchievement = UserAchievement::create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'earned_at' => now(),
                'metadata' => $metadata,
                'is_notified' => false,
            ]);

            DB::commit();
            return $userAchievement;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Check donation amount criteria.
     */
    private function checkDonationAmountCriteria(User $user, $criteria)
    {
        $minAmount = $criteria['min_amount'] ?? 0;
        $currency = $criteria['currency'] ?? 'USD';
        $type = $criteria['donation_type'] ?? null;

        $query = $user->donations()
            ->where('status', 'completed')
            ->where('type', 'monetary');

        if ($type) {
            $query->where('type', $type);
        }

        $totalAmount = $query->sum(DB::raw("JSON_EXTRACT(details, '$.amount')"));

        return $totalAmount >= $minAmount;
    }

    /**
     * Check donation count criteria.
     */
    private function checkDonationCountCriteria(User $user, $criteria)
    {
        $minCount = $criteria['min_count'] ?? 1;
        $type = $criteria['donation_type'] ?? null;
        $status = $criteria['status'] ?? 'completed';

        $query = $user->donations()->where('status', $status);

        if ($type) {
            $query->where('type', $type);
        }

        $count = $query->count();

        return $count >= $minCount;
    }

    /**
     * Check donation type count criteria.
     */
    private function checkDonationTypeCountCriteria(User $user, $criteria)
    {
        $typeCounts = $criteria['type_counts'] ?? [];
        $status = $criteria['status'] ?? 'completed';

        foreach ($typeCounts as $type => $minCount) {
            $count = $user->donations()
                ->where('type', $type)
                ->where('status', $status)
                ->count();

            if ($count < $minCount) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check volunteer completion criteria.
     */
    private function checkVolunteerCompletionCriteria(User $user, $criteria)
    {
        $minCompletions = $criteria['min_completions'] ?? 1;
        $assignmentType = $criteria['assignment_type'] ?? null;

        $query = $user->volunteerAssignments()
            ->where('is_active', true)
            ->where('status', 'completed');

        if ($assignmentType) {
            $query->where('assignment_type', $assignmentType);
        }

        $count = $query->count();

        return $count >= $minCompletions;
    }

    /**
     * Check beneficiary help criteria.
     */
    private function checkBeneficiaryHelpCriteria(User $user, $criteria)
    {
        $minHelped = $criteria['min_helped'] ?? 1;
        $status = $criteria['status'] ?? 'completed';

        // This would need to be implemented based on how you track volunteer help
        // For now, we'll use volunteer assignments as a proxy
        $count = $user->volunteerAssignments()
            ->where('is_active', true)
            ->where('status', $status)
            ->count();

        return $count >= $minHelped;
    }

    /**
     * Check streak criteria.
     */
    private function checkStreakCriteria(User $user, $criteria)
    {
        $minStreak = $criteria['min_streak'] ?? 1;
        $streakType = $criteria['streak_type'] ?? 'donation';

        // This would need to be implemented based on your streak tracking logic
        // For now, we'll return false as it requires more complex logic
        return false;
    }

    /**
     * Check milestone criteria.
     */
    private function checkMilestoneCriteria(User $user, $criteria)
    {
        $milestone = $criteria['milestone'] ?? null;

        switch ($milestone) {
            case 'first_donation':
                return $user->donations()->where('status', 'completed')->count() >= 1;

            case 'first_volunteer':
                return $user->volunteerAssignments()->where('is_active', true)->count() >= 1;

            case 'profile_complete':
                return $this->isProfileComplete($user);

            default:
                return false;
        }
    }

    /**
     * Check special criteria.
     */
    private function checkSpecialCriteria(User $user, $criteria, $triggerData)
    {
        $specialType = $criteria['special_type'] ?? null;

        switch ($specialType) {
            case 'birthday_donation':
                return $this->isBirthdayDonation($user, $triggerData);

            case 'holiday_donation':
                return $this->isHolidayDonation($triggerData);

            case 'large_donation':
                return $this->isLargeDonation($triggerData);

            default:
                return false;
        }
    }

    /**
     * Check if user's profile is complete.
     */
    private function isProfileComplete(User $user)
    {
        return $user->first_name &&
               $user->last_name &&
               $user->email &&
               $user->phone &&
               $user->address;
    }

    /**
     * Check if it's a birthday donation.
     */
    private function isBirthdayDonation(User $user, $triggerData)
    {
        if (!$user->date_of_birth) {
            return false;
        }

        $birthday = $user->date_of_birth;
        $today = now();

        return $birthday->month === $today->month &&
               $birthday->day === $today->day;
    }

    /**
     * Check if it's a holiday donation.
     */
    private function isHolidayDonation($triggerData)
    {
        $today = now();
        $holidays = [
            '12-25', // Christmas
            '01-01', // New Year
            '07-04', // Independence Day (US)
            '11-24', // Thanksgiving (US)
        ];

        $todayFormatted = $today->format('m-d');
        return in_array($todayFormatted, $holidays);
    }

    /**
     * Check if it's a large donation.
     */
    private function isLargeDonation($triggerData)
    {
        $amount = $triggerData['amount'] ?? 0;
        $threshold = $triggerData['threshold'] ?? 10000;

        return $amount >= $threshold;
    }

    /**
     * Get achievement progress for a user.
     */
    public function getAchievementProgress(User $user, Achievement $achievement)
    {
        $criteria = $achievement->criteria;
        $type = $criteria['type'] ?? null;

        switch ($type) {
            case 'donation_amount':
                return $this->getDonationAmountProgress($user, $criteria);

            case 'donation_count':
                return $this->getDonationCountProgress($user, $criteria);

            case 'donation_type_count':
                return $this->getDonationTypeCountProgress($user, $criteria);

            default:
                return ['current' => 0, 'target' => 1, 'percentage' => 0];
        }
    }

    /**
     * Get donation amount progress.
     */
    private function getDonationAmountProgress(User $user, $criteria)
    {
        $minAmount = $criteria['min_amount'] ?? 0;
        $type = $criteria['donation_type'] ?? null;

        $query = $user->donations()
            ->where('status', 'completed')
            ->where('type', 'monetary');

        if ($type) {
            $query->where('type', $type);
        }

        $currentAmount = $query->sum(DB::raw("JSON_EXTRACT(details, '$.amount')"));

        return [
            'current' => $currentAmount,
            'target' => $minAmount,
            'percentage' => $minAmount > 0 ? min(100, ($currentAmount / $minAmount) * 100) : 0,
        ];
    }

    /**
     * Get donation count progress.
     */
    private function getDonationCountProgress(User $user, $criteria)
    {
        $minCount = $criteria['min_count'] ?? 1;
        $type = $criteria['donation_type'] ?? null;
        $status = $criteria['status'] ?? 'completed';

        $query = $user->donations()->where('status', $status);

        if ($type) {
            $query->where('type', $type);
        }

        $currentCount = $query->count();

        return [
            'current' => $currentCount,
            'target' => $minCount,
            'percentage' => $minCount > 0 ? min(100, ($currentCount / $minCount) * 100) : 0,
        ];
    }

    /**
     * Get donation type count progress.
     */
    private function getDonationTypeCountProgress(User $user, $criteria)
    {
        $typeCounts = $criteria['type_counts'] ?? [];
        $status = $criteria['status'] ?? 'completed';

        $totalCurrent = 0;
        $totalTarget = 0;

        foreach ($typeCounts as $type => $minCount) {
            $currentCount = $user->donations()
                ->where('type', $type)
                ->where('status', $status)
                ->count();

            $totalCurrent += min($currentCount, $minCount);
            $totalTarget += $minCount;
        }

        return [
            'current' => $totalCurrent,
            'target' => $totalTarget,
            'percentage' => $totalTarget > 0 ? min(100, ($totalCurrent / $totalTarget) * 100) : 0,
        ];
    }
}
