<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    public function index()
    {
        return view('help.index');
    }

    public function general()
    {
        // If user is authenticated, redirect to role-specific help
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('SUPER_ADMIN')) {
                return redirect()->route('help.admin');
            } elseif ($user->hasRole('VOLUNTEER')) {
                return redirect()->route('help.volunteer');
            } elseif ($user->hasRole('SYSTEM')) {
                return redirect()->route('help.system');
            }
        }

        return view('help.general');
    }

    public function volunteer()
    {
        return view('help.volunteer');
    }

    public function admin()
    {
        return view('help.admin');
    }

    public function system()
    {
        return view('help.system');
    }
}
