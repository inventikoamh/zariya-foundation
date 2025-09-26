<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.system')]
class SystemProfile extends Component
{
    public function render()
    {
        return view('livewire.profile.system-profile');
    }
}
