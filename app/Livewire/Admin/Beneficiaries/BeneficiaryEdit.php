<?php

namespace App\Livewire\Admin\Beneficiaries;

use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class BeneficiaryEdit extends Component
{
    public Beneficiary $beneficiary;

    // Basic information
    public $name = '';
    public $email = '';
    public $phone = '';

    // Request details
    public $category = '';
    public $description = '';
    public $urgency_notes = '';
    public $priority = '';
    public $estimated_amount = '';
    public $currency = '';

    // Location
    public $country_id = '';
    public $state_id = '';
    public $city_id = '';
    public $pincode = '';

    // Management
    public $status = '';
    public $assigned_to = '';

    // Additional info
    public $additional_info = '';

    public $countries = [];
    public $states = [];
    public $cities = [];
    public $volunteers = [];

    public function mount(Beneficiary $beneficiary)
    {
        // Check if current user is the creator of the beneficiary request
        if ($beneficiary->requested_by !== auth()->id()) {
            abort(403, 'You can only edit beneficiary requests that you created.');
        }

        $this->beneficiary = $beneficiary;

        // Load beneficiary data
        $this->name = $beneficiary->name;
        $this->email = $beneficiary->email;
        $this->phone = $beneficiary->phone;
        $this->category = $beneficiary->category;
        $this->description = $beneficiary->description;
        $this->urgency_notes = $beneficiary->urgency_notes;
        $this->priority = $beneficiary->priority;
        $this->estimated_amount = $beneficiary->estimated_amount;
        $this->currency = $beneficiary->currency;
        $this->status = $beneficiary->status;
        $this->assigned_to = $beneficiary->assigned_to;
        $this->additional_info = $beneficiary->additional_info;

        // Load location data
        if ($beneficiary->location) {
            $this->country_id = $beneficiary->location['country_id'] ?? '';
            $this->state_id = $beneficiary->location['state_id'] ?? '';
            $this->city_id = $beneficiary->location['city_id'] ?? '';
            $this->pincode = $beneficiary->location['pincode'] ?? '';
        }

        // Load dropdown data
        $this->countries = Country::orderBy('name')->get();
        $this->volunteers = User::whereHas('roles', function ($query) {
            $query->where('name', 'VOLUNTEER');
        })->orderBy('name')->get();

        if ($this->country_id) {
            $this->states = State::where('country_id', $this->country_id)->orderBy('name')->get();
        }

        if ($this->state_id) {
            $this->cities = City::where('state_id', $this->state_id)->orderBy('name')->get();
        }
    }

    public function updatedCountryId()
    {
        $this->state_id = '';
        $this->city_id = '';
        $this->states = State::where('country_id', $this->country_id)->orderBy('name')->get();
        $this->cities = [];
    }

    public function updatedStateId()
    {
        $this->city_id = '';
        $this->cities = City::where('state_id', $this->state_id)->orderBy('name')->get();
    }

    public function update()
    {
        // Double-check authorization before updating
        if ($this->beneficiary->requested_by !== auth()->id()) {
            abort(403, 'You can only edit beneficiary requests that you created.');
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'category' => 'required|string|in:medical,education,food,shelter,emergency,other',
            'description' => 'required|string|min:10|max:2000',
            'urgency_notes' => 'nullable|string|max:1000',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'estimated_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'pincode' => 'nullable|string|max:10',
            'status' => 'required|string|in:pending,under_review,approved,rejected,fulfilled',
            'assigned_to' => 'nullable|exists:users,id',
            'additional_info' => 'nullable|string|max:1000',
        ]);

        $this->beneficiary->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'category' => $this->category,
            'description' => $this->description,
            'urgency_notes' => $this->urgency_notes,
            'priority' => $this->priority,
            'estimated_amount' => $this->estimated_amount ?: null,
            'currency' => $this->currency,
            'location' => [
                'country_id' => $this->country_id,
                'state_id' => $this->state_id,
                'city_id' => $this->city_id,
                'pincode' => $this->pincode,
            ],
            'status' => $this->status,
            'assigned_to' => $this->assigned_to ?: null,
            'additional_info' => $this->additional_info,
        ]);

        session()->flash('success', 'Beneficiary request updated successfully.');
        return redirect()->route('admin.beneficiaries.show', $this->beneficiary);
    }

    public function render()
    {
        return view('livewire.admin.beneficiaries.beneficiary-edit');
    }
}
