<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class AdminProfile extends Component
{
    public function render()
    {
        return view('livewire.profile.admin-profile');
    }
}
