<?php

namespace App\Livewire\Volunteer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Donation;
use App\Models\Beneficiary;
use App\Models\Remark;
use App\Models\VolunteerAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.volunteer')]
class VolunteerDashboard extends Component
{
    public $stats = [];
    public $recentActivities = [];
    public $myDonations = [];
    public $myAssignments = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadRecentActivities();
        $this->loadMyDonations();
        $this->loadMyAssignments();
    }

    public function loadStats()
    {
        $volunteerId = auth()->id();

        $this->stats = [
            'assignedDonations' => Donation::where('assigned_to', $volunteerId)->count(),
            'completedDonations' => Donation::where('assigned_to', $volunteerId)
                ->where('status', 'completed')
                ->count(),
            'inProgressDonations' => Donation::where('assigned_to', $volunteerId)
                ->whereIn('status', ['in_progress', 'processing'])
                ->count(),
            'pendingDonations' => Donation::where('assigned_to', $volunteerId)
                ->where('status', 'pending')
                ->count(),
            'urgentAssignments' => Donation::where('assigned_to', $volunteerId)
                ->where('is_urgent', true)
                ->count(),
            'thisMonthCompleted' => Donation::where('assigned_to', $volunteerId)
                ->where('status', 'completed')
                ->whereMonth('updated_at', Carbon::now()->month)
                ->count(),
        ];
    }

    public function loadRecentActivities()
    {
        $volunteerId = auth()->id();

        $this->recentActivities = collect([
            // Recent donations assigned to volunteer
            Donation::where('assigned_to', $volunteerId)
                ->with(['donor'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($donation) {
                    return [
                        'type' => 'donation',
                        'message' => "Assigned to handle donation from {$donation->donor->name}",
                        'time' => $donation->assigned_at ?? $donation->created_at,
                        'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
                        'color' => 'green'
                    ];
                }),
            // Recent beneficiaries assigned to volunteer
            Beneficiary::where('assigned_to', $volunteerId)
                ->with(['requestedBy'])
                ->latest()
                ->take(3)
                ->get()
                ->map(function($beneficiary) {
                    return [
                        'type' => 'beneficiary',
                        'message' => "Assigned to handle request from " . ($beneficiary->requestedBy ? $beneficiary->requestedBy->name : $beneficiary->name),
                        'time' => $beneficiary->created_at,
                        'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                        'color' => 'blue'
                    ];
                }),
            // Recent remarks by volunteer
            Remark::where('user_id', $volunteerId)
                ->with(['remarkable'])
                ->latest()
                ->take(3)
                ->get()
                ->map(function($remark) {
                    return [
                        'type' => 'remark',
                        'message' => "Added {$remark->type_label} remark",
                        'time' => $remark->created_at,
                        'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                        'color' => 'yellow'
                    ];
                })
        ])->flatten(1)->sortByDesc('time')->take(8)->values();
    }

    public function loadMyDonations()
    {
        $volunteerId = auth()->id();

        $this->myDonations = Donation::where('assigned_to', $volunteerId)
            ->with(['donor'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($donation) {
                return [
                    'id' => $donation->id,
                    'type' => $donation->type,
                    'amount' => $donation->amount,
                    'donor_name' => $donation->donor->name,
                    'status' => $donation->status,
                    'is_urgent' => $donation->is_urgent,
                    'created_at' => $donation->created_at,
                    'assigned_at' => $donation->assigned_at,
                ];
            });
    }

    public function loadMyAssignments()
    {
        $volunteerId = auth()->id();

        $this->myAssignments = collect([
            // Donations assigned to volunteer
            Donation::where('assigned_to', $volunteerId)
                ->with(['donor'])
                ->latest()
                ->take(3)
                ->get()
                ->map(function($donation) {
                    return [
                        'id' => $donation->id,
                        'type' => 'donation',
                        'title' => "Donation from {$donation->donor->name}",
                        'status' => $donation->status,
                        'is_urgent' => $donation->is_urgent,
                        'created_at' => $donation->assigned_at ?? $donation->created_at,
                    ];
                }),
            // Beneficiaries assigned to volunteer
            Beneficiary::where('assigned_to', $volunteerId)
                ->with(['requestedBy'])
                ->latest()
                ->take(2)
                ->get()
                ->map(function($beneficiary) {
                    return [
                        'id' => $beneficiary->id,
                        'type' => 'beneficiary',
                        'title' => "Request from " . ($beneficiary->requestedBy ? $beneficiary->requestedBy->name : $beneficiary->name),
                        'status' => $beneficiary->status,
                        'is_urgent' => $beneficiary->is_urgent,
                        'created_at' => $beneficiary->created_at,
                    ];
                })
        ])->flatten(1)->sortByDesc('created_at')->take(5)->values();
    }

    public function render()
    {
        return view('livewire.volunteer.volunteer-dashboard');
    }
}
