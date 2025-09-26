<?php

namespace App\Livewire\Admin\Donations;

use App\Models\Donation;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Services\StatusHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class DonationsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $countryFilter = '';
    public $stateFilter = '';
    public $cityFilter = '';
    public $assignedToFilter = '';
    public $isUrgentFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'countryFilter' => ['except' => ''],
        'stateFilter' => ['except' => ''],
        'cityFilter' => ['except' => ''],
        'assignedToFilter' => ['except' => ''],
        'isUrgentFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingCountryFilter()
    {
        $this->resetPage();
        $this->stateFilter = '';
        $this->cityFilter = '';
    }

    public function updatingStateFilter()
    {
        $this->resetPage();
        $this->cityFilter = '';
    }

    public function updatingCityFilter()
    {
        $this->resetPage();
    }

    public function updatingAssignedToFilter()
    {
        $this->resetPage();
    }

    public function updatingIsUrgentFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->priorityFilter = '';
        $this->countryFilter = '';
        $this->stateFilter = '';
        $this->cityFilter = '';
        $this->assignedToFilter = '';
        $this->isUrgentFilter = '';
        $this->resetPage();
    }

    public function getStatusOptionsProperty()
    {
        if ($this->typeFilter) {
            // If a specific type is selected, show only statuses for that type
            return StatusHelper::getStatusOptions($this->typeFilter);
        } else {
            // If no type is selected, show all unique statuses across all types
            $allStatuses = \App\Models\Status::active()->get();
            $uniqueStatuses = $allStatuses->unique('name');
            return $uniqueStatuses->pluck('display_name', 'name')->toArray();
        }
    }

    public function render()
    {
        $donations = Donation::with(['donor', 'assignedTo', 'country', 'state', 'city'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('donor', function ($donorQuery) {
                        $donorQuery->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('details->item_name', 'like', '%' . $this->search . '%')
                    ->orWhere('details->service_type', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter, function ($query) {
                $query->where('priority', $this->priorityFilter);
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('country_id', $this->countryFilter);
            })
            ->when($this->stateFilter, function ($query) {
                $query->where('state_id', $this->stateFilter);
            })
            ->when($this->cityFilter, function ($query) {
                $query->where('city_id', $this->cityFilter);
            })
            ->when($this->assignedToFilter, function ($query) {
                $query->where('assigned_to', $this->assignedToFilter);
            })
            ->when($this->isUrgentFilter !== '', function ($query) {
                $query->where('is_urgent', $this->isUrgentFilter === '1');
            })
            ->orderBy('is_urgent', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $states = State::where('is_active', true)
            ->when($this->countryFilter, function ($query) {
                $query->where('country_id', $this->countryFilter);
            })
            ->orderBy('name')
            ->get();
        $cities = City::where('is_active', true)
            ->when($this->stateFilter, function ($query) {
                $query->where('state_id', $this->stateFilter);
            })
            ->orderBy('name')
            ->get();
        $volunteers = User::role('VOLUNTEER')->orderBy('first_name')->get();

        return view('livewire.admin.donations.donations-index', [
            'donations' => $donations,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
            'volunteers' => $volunteers,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
