<?php

namespace App\Livewire\Admin\Localization;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Models\VolunteerAssignment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class VolunteerAssignmentManage extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingAssignment = null;

    // Form fields
    #[Validate('required|exists:users,id')]
    public $user_id = '';

    #[Validate('required|in:country,state,city')]
    public $assignment_type = '';

    #[Validate('required|in:head_volunteer,volunteer')]
    public $role = 'volunteer';

    #[Validate('nullable|exists:countries,id')]
    public $country_id = '';

    #[Validate('nullable|exists:states,id')]
    public $state_id = '';

    #[Validate('nullable|exists:cities,id')]
    public $city_id = '';

    #[Validate('nullable|string|max:500')]
    public $notes = '';

    public $is_active = true;

    // Filter properties
    public $search = '';
    public $roleFilter = '';
    public $assignmentTypeFilter = '';
    public $countryFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'assignmentTypeFilter' => ['except' => ''],
        'countryFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingAssignmentTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingCountryFilter()
    {
        $this->resetPage();
    }

    public function updatedAssignmentType()
    {
        // Reset dependent fields when assignment type changes
        $this->country_id = '';
        $this->state_id = '';
        $this->city_id = '';
    }

    public function updatedCountryId()
    {
        // Reset state and city when country changes
        $this->state_id = '';
        $this->city_id = '';
    }

    public function updatedStateId()
    {
        // Reset city when state changes
        $this->city_id = '';
    }

    public function openModal($assignmentId = null)
    {
        $this->resetForm();
        
        if ($assignmentId) {
            $this->editingAssignment = VolunteerAssignment::findOrFail($assignmentId);
            $this->user_id = $this->editingAssignment->user_id;
            $this->assignment_type = $this->editingAssignment->assignment_type;
            $this->role = $this->editingAssignment->role;
            $this->country_id = $this->editingAssignment->country_id;
            $this->state_id = $this->editingAssignment->state_id;
            $this->city_id = $this->editingAssignment->city_id;
            $this->notes = $this->editingAssignment->notes;
            $this->is_active = $this->editingAssignment->is_active;
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
        $this->editingAssignment = null;
        $this->user_id = '';
        $this->assignment_type = '';
        $this->role = 'volunteer';
        $this->country_id = '';
        $this->state_id = '';
        $this->city_id = '';
        $this->notes = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        // Additional validation based on assignment type
        if ($this->assignment_type === 'country' && !$this->country_id) {
            $this->addError('country_id', 'Country is required for country assignment.');
            return;
        }

        if ($this->assignment_type === 'state' && (!$this->country_id || !$this->state_id)) {
            $this->addError('state_id', 'Both country and state are required for state assignment.');
            return;
        }

        if ($this->assignment_type === 'city' && (!$this->country_id || !$this->state_id || !$this->city_id)) {
            $this->addError('city_id', 'Country, state, and city are required for city assignment.');
            return;
        }

        // Check for existing head volunteer
        if ($this->role === 'head_volunteer') {
            $existingHeadVolunteer = VolunteerAssignment::where('assignment_type', $this->assignment_type)
                ->where('role', 'head_volunteer')
                ->where('is_active', true)
                ->when($this->country_id, fn($q) => $q->where('country_id', $this->country_id))
                ->when($this->state_id, fn($q) => $q->where('state_id', $this->state_id))
                ->when($this->city_id, fn($q) => $q->where('city_id', $this->city_id))
                ->when($this->editingAssignment, fn($q) => $q->where('id', '!=', $this->editingAssignment->id))
                ->first();

            if ($existingHeadVolunteer) {
                $this->addError('role', 'A head volunteer already exists for this region.');
                return;
            }
        }

        $data = [
            'user_id' => $this->user_id,
            'assignment_type' => $this->assignment_type,
            'role' => $this->role,
            'country_id' => $this->country_id ?: null,
            'state_id' => $this->state_id ?: null,
            'city_id' => $this->city_id ?: null,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        if ($this->editingAssignment) {
            $this->editingAssignment->update($data);
            session()->flash('message', 'Volunteer assignment updated successfully.');
        } else {
            VolunteerAssignment::create($data);
            session()->flash('message', 'Volunteer assignment created successfully.');
        }

        $this->closeModal();
    }

    public function delete($assignmentId)
    {
        $assignment = VolunteerAssignment::findOrFail($assignmentId);
        $assignment->delete();
        
        session()->flash('message', 'Volunteer assignment deleted successfully.');
    }

    public function toggleStatus($assignmentId)
    {
        $assignment = VolunteerAssignment::findOrFail($assignmentId);
        $assignment->update(['is_active' => !$assignment->is_active]);
        
        session()->flash('message', 'Volunteer assignment status updated successfully.');
    }

    public function render()
    {
        $assignments = VolunteerAssignment::query()
            ->with(['user', 'country', 'state', 'city'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->when($this->assignmentTypeFilter, function ($query) {
                $query->where('assignment_type', $this->assignmentTypeFilter);
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('country_id', $this->countryFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['VOLUNTEER', 'SUPER_ADMIN']);
        })->get();

        $countries = Country::active()->get();
        $states = State::active()
            ->when($this->country_id, function ($query) {
                $query->where('country_id', $this->country_id);
            })
            ->get();
        $cities = City::active()
            ->when($this->state_id, function ($query) {
                $query->where('state_id', $this->state_id);
            })
            ->get();

        return view('livewire.admin.localization.volunteer-assignment-manage', [
            'assignments' => $assignments,
            'users' => $users,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
        ]);
    }
}