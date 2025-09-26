<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\Donation;
use App\Models\Beneficiary;
use App\Models\UserAchievement;
use App\Models\VolunteerAssignment;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class UserShow extends Component
{
    public User $user;
    public $userStats;
    public $donations;
    public $beneficiaryRequests;
    public $achievements;
    public $volunteerAssignments;
    public $assignedDonations;
    public $assignedRequests;

    public function mount(User $user)
    {
        $this->user = $user->load(['roles', 'country', 'state', 'city']);
        $this->loadUserData();
    }

    public function loadUserData()
    {
        // Load user's donations
        $this->donations = Donation::where('donor_id', $this->user->id)
            ->with(['assignedTo', 'country', 'state', 'city'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Load user's beneficiary requests
        $this->beneficiaryRequests = Beneficiary::where('requested_by', $this->user->id)
            ->with(['assignedTo', 'country', 'state', 'city'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Load user's achievements
        $this->achievements = UserAchievement::where('user_id', $this->user->id)
            ->with(['achievement'])
            ->orderBy('earned_at', 'desc')
            ->get();

        // Load volunteer assignments if user is a volunteer
        $this->volunteerAssignments = collect();
        if ($this->user->hasRole('VOLUNTEER')) {
            $this->volunteerAssignments = VolunteerAssignment::where('user_id', $this->user->id)
                ->with(['country', 'state', 'city'])
                ->get();
        }

        // Load assigned donations if user is a volunteer
        $this->assignedDonations = collect();
        if ($this->user->hasRole('VOLUNTEER')) {
            $this->assignedDonations = Donation::where('assigned_to', $this->user->id)
                ->with(['donor', 'country', 'state', 'city'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Load assigned requests if user is a volunteer
        $this->assignedRequests = collect();
        if ($this->user->hasRole('VOLUNTEER')) {
            $this->assignedRequests = Beneficiary::where('assigned_to', $this->user->id)
                ->with(['requestedBy', 'country', 'state', 'city'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Calculate user statistics
        $this->userStats = $this->calculateUserStats();
    }

    private function calculateUserStats()
    {
        $stats = [
            'total_donations' => Donation::where('donor_id', $this->user->id)->count(),
            'total_donation_amount' => Donation::where('donor_id', $this->user->id)
                ->where('type', 'monetary')
                ->get()
                ->sum(function($donation) {
                    return is_array($donation->details) && isset($donation->details['amount'])
                        ? (float)$donation->details['amount']
                        : 0;
                }),
            'total_requests' => Beneficiary::where('requested_by', $this->user->id)->count(),
            'total_achievements' => UserAchievement::where('user_id', $this->user->id)->count(),
            'achievement_points' => UserAchievement::where('user_id', $this->user->id)
                ->with('achievement')
                ->get()
                ->sum('achievement.points'),
        ];

        // Add volunteer-specific stats
        if ($this->user->hasRole('VOLUNTEER')) {
            $stats['assigned_donations'] = Donation::where('assigned_to', $this->user->id)->count();
            $stats['completed_donations'] = Donation::where('assigned_to', $this->user->id)
                ->where('status', 'completed')
                ->count();
            $stats['assigned_requests'] = Beneficiary::where('assigned_to', $this->user->id)->count();
            $stats['completed_requests'] = Beneficiary::where('assigned_to', $this->user->id)
                ->where('status', 'fulfilled')
                ->count();
            $stats['volunteer_assignments'] = VolunteerAssignment::where('user_id', $this->user->id)->count();
        }

        return $stats;
    }

    public function render()
    {
        return view('livewire.admin.users.user-show');
    }
}
