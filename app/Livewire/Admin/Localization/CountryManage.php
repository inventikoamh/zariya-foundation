<?php

namespace App\Livewire\Admin\Localization;

use App\Models\Country;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class CountryManage extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingCountry = null;

    // Form fields
    #[Validate('required|string|max:100')]
    public $name = '';

    #[Validate('required|string|max:3|unique:countries,code')]
    public $code = '';

    #[Validate('nullable|string|max:5')]
    public $phone_code = '';

    public $is_active = true;

    // Filter properties
    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal($countryId = null)
    {
        $this->resetForm();
        
        if ($countryId) {
            $this->editingCountry = Country::findOrFail($countryId);
            $this->name = $this->editingCountry->name;
            $this->code = $this->editingCountry->code;
            $this->phone_code = $this->editingCountry->phone_code;
            $this->is_active = $this->editingCountry->is_active;
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
        $this->editingCountry = null;
        $this->name = '';
        $this->code = '';
        $this->phone_code = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        // Update validation rule for editing
        if ($this->editingCountry) {
            $this->validate([
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:3|unique:countries,code,' . $this->editingCountry->id,
                'phone_code' => 'nullable|string|max:5',
            ]);
        } else {
            $this->validate();
        }

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'phone_code' => $this->phone_code,
            'is_active' => $this->is_active,
        ];

        if ($this->editingCountry) {
            $this->editingCountry->update($data);
            session()->flash('message', 'Country updated successfully.');
        } else {
            Country::create($data);
            session()->flash('message', 'Country created successfully.');
        }

        $this->closeModal();
    }

    public function delete($countryId)
    {
        $country = Country::findOrFail($countryId);
        
        // Check if country has states or cities
        if ($country->states()->count() > 0 || $country->cities()->count() > 0) {
            session()->flash('error', 'Cannot delete country with existing states or cities.');
            return;
        }
        
        $country->delete();
        session()->flash('message', 'Country deleted successfully.');
    }

    public function toggleStatus($countryId)
    {
        $country = Country::findOrFail($countryId);
        $country->update(['is_active' => !$country->is_active]);
        
        session()->flash('message', 'Country status updated successfully.');
    }

    public function render()
    {
        $countries = Country::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->withCount(['states', 'cities', 'volunteerAssignments'])
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.localization.country-manage', [
            'countries' => $countries,
        ]);
    }
}