<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout as LWLayout;

class UserDashboard extends Component
{
    public function render()
    {
        $layout = 'layouts.user';
        if (auth()->check()) {
            if (auth()->user()->hasRole('SUPER_ADMIN')) {
                $layout = 'layouts.admin';
            } elseif (auth()->user()->hasRole('VOLUNTEER')) {
                $layout = 'layouts.volunteer';
            }
        }
        return view('livewire.dashboard.user-dashboard')->layout($layout);
    }
}
