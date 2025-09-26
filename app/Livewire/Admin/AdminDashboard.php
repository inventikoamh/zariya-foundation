<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Donation;
use App\Models\Beneficiary;
use App\Models\Remark;
use App\Models\VolunteerAssignment;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.admin')]
class AdminDashboard extends Component
{
    // Core Statistics
    public $kpiStats = [];
    public $recentActivities = [];

    // Chart Data
    public $donationCharts = [];
    public $beneficiaryCharts = [];
    public $userCharts = [];
    public $locationCharts = [];
    public $timeSeriesCharts = [];
    public $statusDistributionCharts = [];

    // Filters
    public $timeFilter = 'monthly'; // monthly, yearly, weekly
    public $statusFilter = 'all';
    public $dateRange = '6_months'; // 1_month, 3_months, 6_months, 1_year, all

    public function mount()
    {
        $this->loadKPIStats();
        $this->loadRecentActivities();
        $this->loadDonationCharts();
        $this->loadBeneficiaryCharts();
        $this->loadUserCharts();
        $this->loadLocationCharts();
        $this->loadTimeSeriesCharts();
        $this->loadStatusDistributionCharts();
    }

    public function loadKPIStats()
    {
        $this->kpiStats = [
            'totalUsers' => User::count(),
            'totalDonations' => Donation::count(),
            'totalBeneficiaries' => Beneficiary::count(),
            'totalAmount' => Donation::where('type', 'monetary')->get()->sum('amount') ?? 0,
            'pendingRequests' => Beneficiary::where('status', 'pending')->count(),
            'activeVolunteers' => DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->where('roles.name', 'VOLUNTEER')
                ->count(),
            'completedDonations' => Donation::where('status', 'completed')->count(),
            'urgentItems' => Donation::where('is_urgent', true)->count() + Beneficiary::where('is_urgent', true)->count(),
        ];
    }

    public function loadRecentActivities()
    {
        $activities = collect([
            // Recent donations
            Donation::with('donor')->latest()->take(5)->get()->map(function($donation) {
                return [
                    'type' => 'donation',
                    'title' => "New {$donation->type} donation",
                    'description' => "From {$donation->donor->name}",
                    'time' => $donation->created_at,
                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
                    'color' => 'green',
                    'amount' => $donation->type === 'monetary' ? $donation->amount : null
                ];
            }),
            // Recent beneficiaries
            Beneficiary::with('requestedBy')->latest()->take(5)->get()->map(function($beneficiary) {
                return [
                    'type' => 'beneficiary',
                    'title' => "New assistance request",
                    'description' => "From " . ($beneficiary->requestedBy ? $beneficiary->requestedBy->name : $beneficiary->name),
                    'time' => $beneficiary->created_at,
                    'icon' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    'color' => 'blue'
                ];
            }),
            // Recent remarks
            Remark::with('user')->latest()->take(5)->get()->map(function($remark) {
                return [
                    'type' => 'remark',
                    'title' => "New {$remark->type_label} remark",
                    'description' => "By {$remark->user->name}",
                    'time' => $remark->created_at,
                    'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                    'color' => 'yellow'
                ];
            })
        ])->flatten(1)->sortByDesc('time')->take(10)->values()->toArray();

        $this->recentActivities = $activities;
    }

    public function loadDonationCharts()
    {
        // Donations by Type (Pie Chart)
        $donationTypes = Donation::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Donations by Status (Bar Chart)
        $donationStatuses = Donation::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Monthly Donation Trends (Line Chart)
        $monthlyTrends = Donation::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $this->getDateRangeStart())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Donation Amount Trends (Line Chart)
        $amountTrends = Donation::where('type', 'monetary')
            ->where('created_at', '>=', $this->getDateRangeStart())
            ->get()
            ->groupBy(function($donation) {
                return $donation->created_at->format('Y-m');
            })
            ->map(function($donations) {
                return $donations->sum('amount');
            })
            ->toArray();

        $this->donationCharts = [
            'byType' => $donationTypes,
            'byStatus' => $donationStatuses,
            'monthlyTrends' => $monthlyTrends,
            'amountTrends' => $amountTrends,
        ];
    }

    public function loadBeneficiaryCharts()
    {
        // Beneficiaries by Status (Pie Chart)
        $beneficiaryStatuses = Beneficiary::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Beneficiaries by Priority (Bar Chart)
        $beneficiaryPriorities = Beneficiary::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // Monthly Beneficiary Trends (Line Chart)
        $monthlyTrends = Beneficiary::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $this->getDateRangeStart())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $this->beneficiaryCharts = [
            'byStatus' => $beneficiaryStatuses,
            'byPriority' => $beneficiaryPriorities,
            'monthlyTrends' => $monthlyTrends,
        ];
    }

    public function loadUserCharts()
    {
        // Users by Role (Pie Chart)
        $userRoles = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->select('roles.name', DB::raw('count(*) as count'))
            ->groupBy('roles.name')
            ->pluck('count', 'name')
            ->toArray();

        // Monthly User Registration Trends (Line Chart)
        $monthlyTrends = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $this->getDateRangeStart())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $this->userCharts = [
            'byRole' => $userRoles,
            'monthlyTrends' => $monthlyTrends,
        ];
    }

    public function loadLocationCharts()
    {
        // Donations by Country (Bar Chart)
        $donationCountries = Donation::select(
                'countries.name as country',
                DB::raw('count(*) as count')
            )
            ->join('countries', 'donations.country_id', '=', 'countries.id')
            ->groupBy('countries.name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'country')
            ->toArray();

        // Beneficiaries by Country (Bar Chart)
        $beneficiaryCountries = Beneficiary::select(
                'countries.name as country',
                DB::raw('count(*) as count')
            )
            ->join('countries', DB::raw('JSON_UNQUOTE(JSON_EXTRACT(beneficiaries.location, "$.country_id"))'), '=', 'countries.id')
            ->groupBy('countries.name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'country')
            ->toArray();

        // Top States for Donations (Bar Chart)
        $donationStates = Donation::select(
                DB::raw('CONCAT(states.name, ", ", countries.name) as state'),
                DB::raw('count(*) as count')
            )
            ->join('states', 'donations.state_id', '=', 'states.id')
            ->join('countries', 'donations.country_id', '=', 'countries.id')
            ->groupBy('states.name', 'countries.name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'state')
            ->toArray();

        // Top States for Beneficiaries (Bar Chart)
        $beneficiaryStates = Beneficiary::select(
                DB::raw('CONCAT(states.name, ", ", countries.name) as state'),
                DB::raw('count(*) as count')
            )
            ->join('states', DB::raw('JSON_UNQUOTE(JSON_EXTRACT(beneficiaries.location, "$.state_id"))'), '=', 'states.id')
            ->join('countries', DB::raw('JSON_UNQUOTE(JSON_EXTRACT(beneficiaries.location, "$.country_id"))'), '=', 'countries.id')
            ->groupBy('states.name', 'countries.name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'state')
            ->toArray();

        $this->locationCharts = [
            'donationCountries' => $donationCountries,
            'beneficiaryCountries' => $beneficiaryCountries,
            'donationStates' => $donationStates,
            'beneficiaryStates' => $beneficiaryStates,
        ];
    }

    public function loadTimeSeriesCharts()
    {
        $periodFormat = $this->getPeriodFormat();

        // Combined Time Series Data
        $donationData = Donation::select(
                DB::raw($periodFormat . ' as period'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $this->getDateRangeStart())
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count', 'period')
            ->toArray();

        $beneficiaryData = Beneficiary::select(
                DB::raw($periodFormat . ' as period'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $this->getDateRangeStart())
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count', 'period')
            ->toArray();

        $this->timeSeriesCharts = [
            'donations' => $donationData,
            'beneficiaries' => $beneficiaryData,
            'periods' => collect(array_keys($donationData))
                ->merge(array_keys($beneficiaryData))
                ->unique()
                ->sort()
                ->values()
                ->toArray(),
        ];
    }

    public function loadStatusDistributionCharts()
    {
        // Status Distribution for Donations and Beneficiaries
        $donationStatusDistribution = Donation::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $beneficiaryStatusDistribution = Beneficiary::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->statusDistributionCharts = [
            'donations' => $donationStatusDistribution,
            'beneficiaries' => $beneficiaryStatusDistribution,
        ];
    }

    private function getDateRangeStart()
    {
        return match($this->dateRange) {
            '1_month' => Carbon::now()->subMonth(),
            '3_months' => Carbon::now()->subMonths(3),
            '6_months' => Carbon::now()->subMonths(6),
            '1_year' => Carbon::now()->subYear(),
            'all' => Carbon::create(2020, 1, 1),
            default => Carbon::now()->subMonths(6),
        };
    }

    private function getPeriodFormat()
    {
        return match($this->timeFilter) {
            'weekly' => 'DATE_FORMAT(created_at, "%Y-%u")',
            'monthly' => 'DATE_FORMAT(created_at, "%Y-%m")',
            'yearly' => 'YEAR(created_at)',
            default => 'DATE_FORMAT(created_at, "%Y-%m")',
        };
    }

    public function updatedTimeFilter()
    {
        $this->loadTimeSeriesCharts();
    }

    public function updatedDateRange()
    {
        $this->loadTimeSeriesCharts();
        $this->loadDonationCharts();
        $this->loadBeneficiaryCharts();
        $this->loadUserCharts();
    }

    public function updatedStatusFilter()
    {
        // Reload charts that depend on status filter
        $this->loadDonationCharts();
        $this->loadBeneficiaryCharts();
        $this->loadStatusDistributionCharts();
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard');
    }
}
