<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalDonations = 0; // Placeholder - will be implemented later
        $pendingRequests = 0; // Placeholder - will be implemented later

        return view('admin.dashboard', compact('totalUsers', 'totalDonations', 'pendingRequests'));
    }
}
