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
}
