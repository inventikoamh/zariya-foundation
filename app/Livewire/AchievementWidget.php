<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\UserAchievement;
use Livewire\Component;

class AchievementWidget extends Component
{
    public $userId;
    public $showCount = 5; // Number of recent achievements to show

    public function mount($userId = null)
    {
        $this->userId = $userId ?? auth()->id();
    }

    public function getRecentAchievementsProperty()
    {
        return UserAchievement::with('achievement')
            ->where('user_id', $this->userId)
            ->orderBy('earned_at', 'desc')
            ->limit($this->showCount)
            ->get();
    }

    public function getStatsProperty()
    {
        $user = \App\Models\User::find($this->userId);

        return [
            'total_earned' => $user->achievements()->count(),
            'total_points' => $user->total_achievement_points,
            'recent_count' => $this->recentAchievements->count(),
        ];
    }

    public function getNextAchievementsProperty()
    {
        $user = \App\Models\User::find($this->userId);
        $earnedAchievementIds = $user->achievements()->pluck('achievement_id')->toArray();

        return Achievement::active()
            ->available()
            ->whereNotIn('id', $earnedAchievementIds)
            ->orderBy('points', 'desc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.achievement-widget', [
            'recentAchievements' => $this->recentAchievements,
            'stats' => $this->stats,
            'nextAchievements' => $this->nextAchievements,
        ]);
    }
}
