<?php

namespace App\Livewire\Admin\Localization;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\VolunteerAssignment;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class LocalizationIndex extends Component
{
    use WithPagination;

    public $activeTab = 'countries';
    public $search = '';
    public $selectedCountry = '';
    public $selectedState = '';

    protected $queryString = [
        'activeTab' => ['except' => 'countries'],
        'search' => ['except' => ''],
        'selectedCountry' => ['except' => ''],
        'selectedState' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCountry()
    {
        $this->resetPage();
        $this->selectedState = '';
    }

    public function updatingSelectedState()
    {
        $this->resetPage();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $countries = collect();
        $states = collect();
        $cities = collect();
        $volunteerAssignments = collect();

        switch ($this->activeTab) {
            case 'countries':
                $countries = Country::query()
                    ->when($this->search, function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('code', 'like', '%' . $this->search . '%');
                    })
                    ->withCount(['states', 'cities', 'volunteerAssignments'])
                    ->paginate(10);
                break;

            case 'states':
                $states = State::query()
                    ->with('country')
                    ->when($this->search, function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('code', 'like', '%' . $this->search . '%');
                    })
                    ->when($this->selectedCountry, function ($query) {
                        $query->where('country_id', $this->selectedCountry);
                    })
                    ->withCount(['cities', 'volunteerAssignments'])
                    ->paginate(10);
                break;

            case 'cities':
                $cities = City::query()
                    ->with(['state', 'country'])
                    ->when($this->search, function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('pincode', 'like', '%' . $this->search . '%');
                    })
                    ->when($this->selectedCountry, function ($query) {
                        $query->where('country_id', $this->selectedCountry);
                    })
                    ->when($this->selectedState, function ($query) {
                        $query->where('state_id', $this->selectedState);
                    })
                    ->withCount('volunteerAssignments')
                    ->paginate(10);
                break;

            case 'volunteers':
                $volunteerAssignments = VolunteerAssignment::query()
                    ->with(['user', 'country', 'state', 'city'])
                    ->when($this->search, function ($query) {
                        $query->whereHas('user', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->when($this->selectedCountry, function ($query) {
                        $query->where('country_id', $this->selectedCountry);
                    })
                    ->when($this->selectedState, function ($query) {
                        $query->where('state_id', $this->selectedState);
                    })
                    ->paginate(10);
                break;
        }

        $countriesList = Country::active()->get();
        $statesList = State::active()
            ->when($this->selectedCountry, function ($query) {
                $query->where('country_id', $this->selectedCountry);
            })
            ->get();

        return view('livewire.admin.localization.localization-index', [
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
            'volunteerAssignments' => $volunteerAssignments,
            'countriesList' => $countriesList,
            'statesList' => $statesList,
        ]);
    }
}