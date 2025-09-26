<?php

namespace App\Livewire\Volunteer;

use App\Models\UserAchievement;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.volunteer')]
class VolunteerAchievements extends Component
{
    use WithPagination;

    public function render()
    {
        $userAchievements = UserAchievement::where('user_id', auth()->id())
            ->with(['achievement'])
            ->orderBy('earned_at', 'desc')
            ->paginate(10);

        $totalAchievements = UserAchievement::where('user_id', auth()->id())->count();
        $recentAchievements = UserAchievement::where('user_id', auth()->id())
            ->with(['achievement'])
            ->orderBy('earned_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.volunteer.volunteer-achievements', [
            'userAchievements' => $userAchievements,
            'totalAchievements' => $totalAchievements,
            'recentAchievements' => $recentAchievements,
        ]);
    }
}
