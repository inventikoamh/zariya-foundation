<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.volunteer')]
class VolunteerProfile extends Component
{
    public function render()
    {
        return view('livewire.profile.volunteer-profile');
    }
}
