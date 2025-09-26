<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Donation;
use App\Models\Beneficiary;
use App\Models\Remark;
use App\Models\VolunteerAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.admin')]
class AdminDashboard extends Component
{
    public $stats = [];
    public $recentActivities = [];
    public $donationStats = [];
    public $userStats = [];
    public $beneficiaryStats = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadRecentActivities();
        $this->loadDonationStats();
        $this->loadUserStats();
        $this->loadBeneficiaryStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'totalUsers' => User::count(),
            'totalDonations' => Donation::count(),
            'totalBeneficiaries' => Beneficiary::count(),
            'pendingRequests' => Beneficiary::where('status', 'pending')->count(),
            'activeVolunteers' => User::whereHas('roles', function($q) {
                $q->where('name', 'VOLUNTEER');
            })->count(),
            'totalAmount' => Donation::where('type', 'monetary')->sum('amount') ?? 0,
            'completedDonations' => Donation::where('status', 'completed')->count(),
            'urgentItems' => Donation::where('is_urgent', true)->count() +
                           Beneficiary::where('is_urgent', true)->count(),
        ];
    }

    public function loadRecentActivities()
    {
        $this->recentActivities = collect([
            // Recent donations
            Donation::with('donor')->latest()->take(3)->get()->map(function($donation) {
                return [
                    'type' => 'donation',
                    'message' => "New {$donation->type} donation from {$donation->donor->name}",
                    'time' => $donation->created_at,
                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
                    'color' => 'green'
                ];
            }),
            // Recent beneficiaries
            Beneficiary::with('user')->latest()->take(3)->get()->map(function($beneficiary) {
                return [
                    'type' => 'beneficiary',
                    'message' => "New assistance request from {$beneficiary->user->name}",
                    'time' => $beneficiary->created_at,
                    'icon' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    'color' => 'blue'
                ];
            }),
            // Recent remarks
            Remark::with(['remarkable', 'user'])->latest()->take(3)->get()->map(function($remark) {
                return [
                    'type' => 'remark',
                    'message' => "New {$remark->type_label} remark added",
                    'time' => $remark->created_at,
                    'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                    'color' => 'yellow'
                ];
            })
        ])->flatten(1)->sortByDesc('time')->take(10)->values();
    }

    public function loadDonationStats()
    {
        $this->donationStats = [
            'byType' => Donation::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'byStatus' => Donation::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'monthly' => Donation::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray(),
            'amountByMonth' => Donation::where('type', 'monetary')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray(),
        ];
    }

    public function loadUserStats()
    {
        $this->userStats = [
            'byRole' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('roles.name', DB::raw('count(*) as count'))
                ->groupBy('roles.name')
                ->pluck('count', 'name')
                ->toArray(),
            'monthly' => User::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray(),
        ];
    }

    public function loadBeneficiaryStats()
    {
        $this->beneficiaryStats = [
            'byStatus' => Beneficiary::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'monthly' => Beneficiary::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard');
    }
}
