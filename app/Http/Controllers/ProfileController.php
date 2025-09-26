<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Determine the appropriate view based on user role
        if ($user->hasRole('SUPER_ADMIN')) {
            return view('profile.admin');
        } elseif ($user->hasRole('VOLUNTEER')) {
            return view('profile.volunteer');
        } elseif ($user->hasRole('SYSTEM')) {
            return view('profile.system');
        } else {
            return view('profile.user');
        }
    }

    public function admin()
    {
        return redirect()->route('admin.profile.livewire');
    }

    public function volunteer()
    {
        return redirect()->route('volunteer.profile.livewire');
    }

    public function system()
    {
        return redirect()->route('system.profile.livewire');
    }

    public function user()
    {
        return redirect()->route('profile.livewire');
    }
}
