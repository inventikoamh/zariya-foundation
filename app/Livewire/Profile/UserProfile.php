<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.user')]
class UserProfile extends Component
{
    public function render()
    {
        return view('livewire.profile.user-profile');
    }
}
