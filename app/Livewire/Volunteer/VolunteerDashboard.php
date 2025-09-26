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
            'assignedDonations' => VolunteerAssignment::where('volunteer_id', $volunteerId)
                ->whereHas('donation')
                ->count(),
            'completedDonations' => VolunteerAssignment::where('volunteer_id', $volunteerId)
                ->whereHas('donation', function($q) {
                    $q->where('status', 'completed');
                })
                ->count(),
            'inProgressDonations' => VolunteerAssignment::where('volunteer_id', $volunteerId)
                ->whereHas('donation', function($q) {
                    $q->whereIn('status', ['in_progress', 'processing']);
                })
                ->count(),
            'pendingDonations' => VolunteerAssignment::where('volunteer_id', $volunteerId)
                ->whereHas('donation', function($q) {
                    $q->where('status', 'pending');
                })
                ->count(),
            'urgentAssignments' => VolunteerAssignment::where('volunteer_id', $volunteerId)
                ->whereHas('donation', function($q) {
                    $q->where('is_urgent', true);
                })
                ->count(),
            'thisMonthCompleted' => VolunteerAssignment::where('volunteer_id', $volunteerId)
                ->whereHas('donation', function($q) {
                    $q->where('status', 'completed')
                      ->whereMonth('updated_at', Carbon::now()->month);
                })
                ->count(),
        ];
    }

    public function loadRecentActivities()
    {
        $volunteerId = auth()->id();

        $this->recentActivities = collect([
            // Recent assignments
            VolunteerAssignment::where('volunteer_id', $volunteerId)
                ->with(['donation.donor', 'beneficiary.requestedBy'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($assignment) {
                    $type = $assignment->donation ? 'donation' : 'beneficiary';
                    $name = $assignment->donation ?
                        $assignment->donation->donor->name :
                        ($assignment->beneficiary->requestedBy ? $assignment->beneficiary->requestedBy->name : $assignment->beneficiary->name);

                    return [
                        'type' => $type,
                        'message' => "Assigned to handle {$type} from {$name}",
                        'time' => $assignment->created_at,
                        'icon' => $type === 'donation' ?
                            'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1' :
                            'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                        'color' => $type === 'donation' ? 'green' : 'blue'
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

        $this->myDonations = VolunteerAssignment::where('volunteer_id', $volunteerId)
            ->whereHas('donation')
            ->with(['donation.donor', 'donation.status'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($assignment) {
                $donation = $assignment->donation;
                return [
                    'id' => $donation->id,
                    'type' => $donation->type,
                    'amount' => $donation->amount,
                    'donor_name' => $donation->donor->name,
                    'status' => $donation->status,
                    'is_urgent' => $donation->is_urgent,
                    'created_at' => $donation->created_at,
                    'assigned_at' => $assignment->created_at,
                ];
            });
    }

    public function loadMyAssignments()
    {
        $volunteerId = auth()->id();

        $this->myAssignments = VolunteerAssignment::where('volunteer_id', $volunteerId)
            ->with(['donation.donor', 'beneficiary.requestedBy'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($assignment) {
                if ($assignment->donation) {
                    return [
                        'id' => $assignment->donation->id,
                        'type' => 'donation',
                        'title' => "Donation from {$assignment->donation->donor->name}",
                        'status' => $assignment->donation->status,
                        'is_urgent' => $assignment->donation->is_urgent,
                        'created_at' => $assignment->created_at,
                    ];
                } else {
                    return [
                        'id' => $assignment->beneficiary->id,
                        'type' => 'beneficiary',
                        'title' => "Request from " . ($assignment->beneficiary->requestedBy ? $assignment->beneficiary->requestedBy->name : $assignment->beneficiary->name),
                        'status' => $assignment->beneficiary->status,
                        'is_urgent' => $assignment->beneficiary->is_urgent,
                        'created_at' => $assignment->created_at,
                    ];
                }
            });
    }

    public function render()
    {
        return view('livewire.volunteer.volunteer-dashboard');
    }
}
