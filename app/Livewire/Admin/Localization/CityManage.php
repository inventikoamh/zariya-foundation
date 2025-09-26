<?php

namespace App\Livewire\Admin\Localization;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class CityManage extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingCity = null;

    // Form fields
    #[Validate('required|string|max:100')]
    public $name = '';

    #[Validate('nullable|string|max:10')]
    public $pincode = '';

    #[Validate('required|exists:states,id')]
    public $state_id = '';

    #[Validate('required|exists:countries,id')]
    public $country_id = '';

    public $is_active = true;

    // Filter properties
    public $search = '';
    public $countryFilter = '';
    public $stateFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'countryFilter' => ['except' => ''],
        'stateFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCountryFilter()
    {
        $this->resetPage();
        $this->stateFilter = ''; // Reset state filter when country changes
    }

    public function updatingStateFilter()
    {
        $this->resetPage();
    }

    public function openModal($cityId = null)
    {
        $this->resetForm();
        
        if ($cityId) {
            $this->editingCity = City::findOrFail($cityId);
            $this->name = $this->editingCity->name;
            $this->pincode = $this->editingCity->pincode;
            $this->state_id = $this->editingCity->state_id;
            $this->country_id = $this->editingCity->country_id;
            $this->is_active = $this->editingCity->is_active;
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingCity = null;
        $this->name = '';
        $this->pincode = '';
        $this->state_id = '';
        $this->country_id = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        // Update validation rule for editing
        if ($this->editingCity) {
            $this->validate([
                'name' => 'required|string|max:100',
                'pincode' => 'nullable|string|max:10',
                'state_id' => 'required|exists:states,id',
                'country_id' => 'required|exists:countries,id',
            ]);
        } else {
            $this->validate();
        }

        $data = [
            'name' => $this->name,
            'pincode' => $this->pincode ?: null,
            'state_id' => $this->state_id,
            'country_id' => $this->country_id,
            'is_active' => $this->is_active,
        ];

        if ($this->editingCity) {
            $this->editingCity->update($data);
            session()->flash('message', 'City updated successfully.');
        } else {
            City::create($data);
            session()->flash('message', 'City created successfully.');
        }

        $this->closeModal();
    }

    public function delete($cityId)
    {
        $city = City::findOrFail($cityId);
        $city->delete();
        session()->flash('message', 'City deleted successfully.');
    }

    public function toggleStatus($cityId)
    {
        $city = City::findOrFail($cityId);
        $city->update(['is_active' => !$city->is_active]);
        
        session()->flash('message', 'City status updated successfully.');
    }

    public function render()
    {
        $cities = City::query()
            ->with(['state', 'country'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('pincode', 'like', '%' . $this->search . '%');
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('country_id', $this->countryFilter);
            })
            ->when($this->stateFilter, function ($query) {
                $query->where('state_id', $this->stateFilter);
            })
            ->withCount('volunteerAssignments')
            ->orderBy('name')
            ->paginate(10);

        $countries = Country::active()->orderBy('name')->get();
        $states = State::active()
            ->when($this->country_id, function ($query) {
                $query->where('country_id', $this->country_id);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.localization.city-manage', [
            'cities' => $cities,
            'countries' => $countries,
            'states' => $states,
        ]);
    }
}