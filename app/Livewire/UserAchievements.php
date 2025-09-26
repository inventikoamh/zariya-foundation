<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Services\AchievementService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.user')]
class UserAchievements extends Component
{
    use WithPagination;

    public $userId;
    public $showEarned = true;
    public $showAvailable = true;
    public $typeFilter = '';
    public $categoryFilter = '';
    public $rarityFilter = '';

    public function mount($userId = null)
    {
        $this->userId = $userId ?? auth()->id();
    }

    public function toggleEarned()
    {
        $this->showEarned = !$this->showEarned;
        $this->resetPage();
    }

    public function toggleAvailable()
    {
        $this->showAvailable = !$this->showAvailable;
        $this->resetPage();
    }

    public function getEarnedAchievementsProperty()
    {
        if (!$this->showEarned) {
            return collect();
        }

        $query = UserAchievement::with('achievement')
            ->where('user_id', $this->userId)
            ->orderBy('earned_at', 'desc');

        if ($this->typeFilter) {
            $query->whereHas('achievement', function ($q) {
                $q->where('type', $this->typeFilter);
            });
        }

        if ($this->categoryFilter) {
            $query->whereHas('achievement', function ($q) {
                $q->where('category', $this->categoryFilter);
            });
        }

        if ($this->rarityFilter) {
            $query->whereHas('achievement', function ($q) {
                $q->where('rarity', $this->rarityFilter);
            });
        }

        return $query->get();
    }

    public function getAvailableAchievementsProperty()
    {
        if (!$this->showAvailable) {
            return collect();
        }

        $user = \App\Models\User::find($this->userId);
        $earnedAchievementIds = $user->achievements()->pluck('achievement_id')->toArray();

        $query = Achievement::active()
            ->available()
            ->whereNotIn('id', $earnedAchievementIds);

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->rarityFilter) {
            $query->where('rarity', $this->rarityFilter);
        }

        return $query->orderBy('points', 'desc')->get();
    }

    public function getAchievementProgressProperty()
    {
        $user = \App\Models\User::find($this->userId);
        $achievementService = new AchievementService();
        $progress = [];

        foreach ($this->availableAchievements as $achievement) {
            $progress[$achievement->id] = $achievementService->getAchievementProgress($user, $achievement);
        }

        return $progress;
    }

    public function getStatsProperty()
    {
        $user = \App\Models\User::find($this->userId);

        return [
            'total_earned' => $user->achievements()->count(),
            'total_points' => $user->total_achievement_points,
            'total_available' => Achievement::active()->available()->count(),
            'by_type' => $user->achievements()
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'by_rarity' => $user->achievements()
                ->selectRaw('rarity, count(*) as count')
                ->groupBy('rarity')
                ->pluck('count', 'rarity'),
        ];
    }

    public function getTypeOptionsProperty()
    {
        return [
            'donation' => 'Donation',
            'volunteer' => 'Volunteer',
            'general' => 'General',
        ];
    }

    public function getCategoryOptionsProperty()
    {
        return [
            'monetary' => 'Monetary',
            'materialistic' => 'Materialistic',
            'service' => 'Service',
            'completion' => 'Completion',
            'milestone' => 'Milestone',
            'streak' => 'Streak',
            'special' => 'Special',
        ];
    }

    public function getRarityOptionsProperty()
    {
        return [
            'common' => 'Common',
            'uncommon' => 'Uncommon',
            'rare' => 'Rare',
            'epic' => 'Epic',
            'legendary' => 'Legendary',
        ];
    }

    public function render()
    {
        return view('livewire.user-achievements', [
            'earnedAchievements' => $this->earnedAchievements,
            'availableAchievements' => $this->availableAchievements,
            'achievementProgress' => $this->achievementProgress,
            'stats' => $this->stats,
            'typeOptions' => $this->typeOptions,
            'categoryOptions' => $this->categoryOptions,
            'rarityOptions' => $this->rarityOptions,
        ]);
    }
}
