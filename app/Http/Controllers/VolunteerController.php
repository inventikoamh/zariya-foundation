<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    public function index()
    {
        $assignedItems = 0; // Placeholder - will be implemented later
        $availableItems = 0; // Placeholder - will be implemented later

        return view('volunteer.dashboard', compact('assignedItems', 'availableItems'));
    }
}
