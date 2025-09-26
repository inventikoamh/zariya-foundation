<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use App\Models\SystemSetting;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Services\VolunteerRoutingService;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

#[Layout('layouts.user')]
class BeneficiaryRequest extends Component
{
    // Basic information
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('nullable|email|max:255')]
    public $email = '';

    #[Validate('nullable|string|max:20')]
    public $phone = '';

    // Request details
    #[Validate('required|string|in:medical,education,food,shelter,emergency,other')]
    public $category = '';

    #[Validate('required|string|min:10|max:2000')]
    public $description = '';

    #[Validate('nullable|string|max:1000')]
    public $urgency_notes = '';

    // Priority is set by admins/volunteers, not by general users
    // Default priority will be set to 'medium' in the store method

    #[Validate('nullable|numeric|min:0')]
    public $estimated_amount = '';

    #[Validate('required|string|size:3')]
    public $currency = 'USD';

    // Location
    #[Validate('required|exists:countries,id')]
    public $country_id = '';

    #[Validate('required|exists:states,id')]
    public $state_id = '';

    #[Validate('required|exists:cities,id')]
    public $city_id = '';

    #[Validate('nullable|string|max:10')]
    public $pincode = '';

    // Additional info
    #[Validate('nullable|string|max:1000')]
    public $additional_info = '';

    public $countries = [];
    public $states = [];
    public $cities = [];

    public function mount()
    {
        $this->countries = Country::orderBy('name')->get();

        // Set default currency from system settings
        $this->currency = SystemSetting::get('currency', 'USD');

        // If user is logged in, pre-fill some information
        if (auth()->check()) {
            $user = auth()->user();
            $this->name = $user->first_name . ' ' . $user->last_name;
            $this->email = $user->email;
            $this->phone = $user->phone;

            if ($user->country_id) {
                $this->country_id = $user->country_id;
                $this->states = State::where('country_id', $this->country_id)->orderBy('name')->get();
            }

            if ($user->state_id) {
                $this->state_id = $user->state_id;
                $this->cities = City::where('state_id', $this->state_id)->orderBy('name')->get();
            }

            if ($user->city_id) {
                $this->city_id = $user->city_id;
            }
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

    public function submitRequest()
    {
        $this->validate();

        try {
            $beneficiary = Beneficiary::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'category' => $this->category,
                'description' => $this->description,
                'urgency_notes' => $this->urgency_notes,
                'priority' => 'medium', // Default priority, can be changed by admins/volunteers
                'estimated_amount' => $this->estimated_amount ?: null,
                'currency' => $this->currency,
                'location' => [
                    'country_id' => $this->country_id,
                    'state_id' => $this->state_id,
                    'city_id' => $this->city_id,
                    'pincode' => $this->pincode,
                ],
                'additional_info' => $this->additional_info ?: null,
                'requested_by' => auth()->id(),
                'status' => Beneficiary::STATUS_PENDING,
            ]);

            // Auto-route to appropriate head volunteer (exclude the creator)
            $routingService = app(VolunteerRoutingService::class);
            $assignedVolunteer = $routingService->findNearestHeadVolunteer(
                $this->city_id,
                $this->state_id,
                $this->country_id,
                auth()->id() // Exclude the creator from being assigned
            );

            if ($assignedVolunteer) {
                $beneficiary->update([
                    'assigned_to' => $assignedVolunteer->id,
                    'status' => Beneficiary::STATUS_UNDER_REVIEW,
                ]);

                // Add assignment remark
                $beneficiary->remarks()->create([
                    'user_id' => auth()->id(),
                    'type' => 'assignment',
                    'remark' => "Auto-assigned to {$assignedVolunteer->first_name} {$assignedVolunteer->last_name}",
                ]);
            }

            // Add initial remark
            $beneficiary->remarks()->create([
                'user_id' => auth()->id(),
                'type' => 'general',
                'remark' => 'Assistance request submitted successfully',
            ]);

            $successMessage = 'Your assistance request has been submitted successfully!';
            if ($assignedVolunteer) {
                $successMessage .= " It has been automatically assigned to a volunteer in your area for review.";
            } else {
                $successMessage .= " We will review it and get back to you soon.";
            }

            session()->flash('success', $successMessage);

            // Reset form
            $this->reset(['name', 'email', 'phone', 'category', 'description', 'urgency_notes', 'priority', 'estimated_amount', 'currency', 'country_id', 'state_id', 'city_id', 'pincode', 'additional_info']);
            $this->currency = SystemSetting::get('currency', 'USD');
            $this->states = [];
            $this->cities = [];

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while submitting your request. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.beneficiary-request');
    }
}
