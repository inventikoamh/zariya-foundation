<?php

namespace App\Livewire\Admin\Localization;

use App\Models\State;
use App\Models\Country;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class StateManage extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingState = null;

    // Form fields
    #[Validate('required|string|max:100')]
    public $name = '';

    #[Validate('nullable|string|max:10')]
    public $code = '';

    #[Validate('required|exists:countries,id')]
    public $country_id = '';

    public $is_active = true;

    // Filter properties
    public $search = '';
    public $countryFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'countryFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCountryFilter()
    {
        $this->resetPage();
    }

    public function openModal($stateId = null)
    {
        $this->resetForm();
        
        if ($stateId) {
            $this->editingState = State::findOrFail($stateId);
            $this->name = $this->editingState->name;
            $this->code = $this->editingState->code;
            $this->country_id = $this->editingState->country_id;
            $this->is_active = $this->editingState->is_active;
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
        $this->editingState = null;
        $this->name = '';
        $this->code = '';
        $this->country_id = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        // Update validation rule for editing
        if ($this->editingState) {
            $this->validate([
                'name' => 'required|string|max:100',
                'code' => 'nullable|string|max:10',
                'country_id' => 'required|exists:countries,id',
            ]);
        } else {
            $this->validate();
        }

        $data = [
            'name' => $this->name,
            'code' => $this->code ?: null,
            'country_id' => $this->country_id,
            'is_active' => $this->is_active,
        ];

        if ($this->editingState) {
            $this->editingState->update($data);
            session()->flash('message', 'State updated successfully.');
        } else {
            State::create($data);
            session()->flash('message', 'State created successfully.');
        }

        $this->closeModal();
    }

    public function delete($stateId)
    {
        $state = State::findOrFail($stateId);
        
        // Check if state has cities
        if ($state->cities()->count() > 0) {
            session()->flash('error', 'Cannot delete state with existing cities.');
            return;
        }
        
        $state->delete();
        session()->flash('message', 'State deleted successfully.');
    }

    public function toggleStatus($stateId)
    {
        $state = State::findOrFail($stateId);
        $state->update(['is_active' => !$state->is_active]);
        
        session()->flash('message', 'State status updated successfully.');
    }

    public function render()
    {
        $states = State::query()
            ->with('country')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('country_id', $this->countryFilter);
            })
            ->withCount(['cities', 'volunteerAssignments'])
            ->orderBy('name')
            ->paginate(10);

        $countries = Country::active()->orderBy('name')->get();

        return view('livewire.admin.localization.state-manage', [
            'states' => $states,
            'countries' => $countries,
        ]);
    }
}