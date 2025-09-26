<?php

namespace App\Livewire\Volunteer;

use App\Models\Donation;
use App\Models\SystemSetting;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Services\VolunteerRoutingService;
use App\Services\EmailNotificationService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

#[Layout('layouts.volunteer')]
class VolunteerDonationSubmit extends Component
{
    use WithFileUploads;

    // Basic donation fields
    #[Validate('required|in:monetary,materialistic,service')]
    public $type = '';

    #[Validate('required|exists:countries,id')]
    public $country_id = '';

    #[Validate('nullable|exists:states,id')]
    public $state_id = '';

    #[Validate('nullable|exists:cities,id')]
    public $city_id = '';

    #[Validate('nullable|string|max:10')]
    public $pincode = '';

    #[Validate('nullable|string|max:500')]
    public $address = '';

    #[Validate('nullable|string|max:1000')]
    public $notes = '';

    #[Validate('boolean')]
    public $is_urgent = false;

    // Priority is set by admins/volunteers, not by general users
    // Default priority will be set to 1 (Low) in the submitDonation method

    // Type-specific fields
    // Monetary
    #[Validate('required_if:type,monetary|numeric|min:0')]
    public $amount = '';

    #[Validate('required_if:type,monetary|string|max:10')]
    public $currency = 'USD';

    #[Validate('required_if:type,monetary|string|max:50')]
    public $payment_method = '';

    // Materialistic
    #[Validate('required_if:type,materialistic|string|max:100')]
    public $item_name = '';

    #[Validate('required_if:type,materialistic|string|max:1000')]
    public $item_description = '';

    #[Validate('required_if:type,materialistic|string|max:20')]
    public $alternate_phone = '';

    public $item_images = [];
    public $image_1 = null;
    public $image_2 = null;
    public $image_3 = null;
    public $image_4 = null;
    public $image_5 = null;

    // Service
    #[Validate('required_if:type,service|string|max:100')]
    public $service_type = '';

    #[Validate('required_if:type,service|string|max:1000')]
    public $service_description = '';

    #[Validate('required_if:type,service|string|max:200')]
    public $availability = '';

    public $countries;
    public $states = [];
    public $cities = [];
    public $showSuccessMessage = false;
    public $submittedDonation = null;

    public function mount()
    {
        $this->countries = Country::where('is_active', true)->orderBy('name')->get();
        $this->item_images = []; // Initialize empty array
        // Set default currency from settings
        $this->currency = SystemSetting::get('currency', 'USD');
    }

    public function updatedCountryId()
    {
        $this->state_id = '';
        $this->city_id = '';
        $this->states = State::where('country_id', $this->country_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $this->cities = [];
    }

    public function updatedStateId()
    {
        $this->city_id = '';
        $this->cities = City::where('state_id', $this->state_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function updatedType()
    {
        // Reset type-specific fields when type changes
        $this->amount = '';
        $this->currency = SystemSetting::get('currency', 'USD');
        $this->payment_method = '';
        $this->item_name = '';
        $this->item_description = '';
        $this->alternate_phone = '';
        $this->item_images = $this->type === 'materialistic' ? [null] : [];
        $this->service_type = '';
        $this->service_description = '';
        $this->availability = '';
    }

    public function addImage()
    {
        if (count($this->item_images) < 5) {
            $this->item_images[] = null;
        }
    }

    public function removeImage($index)
    {
        if (isset($this->item_images[$index])) {
            unset($this->item_images[$index]);
            $this->item_images = array_values($this->item_images);
        }
    }

    public function submitDonation()
    {
        $this->validate();

        try {
            // Prepare donation data
            $donationData = [
                'type' => $this->type,
                'donor_id' => auth()->id(),
                'country_id' => $this->country_id,
                'state_id' => $this->state_id,
                'city_id' => $this->city_id,
                'pincode' => $this->pincode,
                'address' => $this->address,
                'notes' => $this->notes,
                'is_urgent' => $this->is_urgent,
                'priority' => 1, // Default priority (Low), can be changed by admins/volunteers
                'status' => 'pending',
            ];

            // Add type-specific data
            if ($this->type === 'monetary') {
                $donationData['details'] = [
                    'amount' => $this->amount,
                    'currency' => $this->currency,
                    'payment_method' => $this->payment_method,
                ];
            } elseif ($this->type === 'materialistic') {
                $donationData['details'] = [
                    'item_name' => $this->item_name,
                    'item_description' => $this->item_description,
                    'alternate_phone' => $this->alternate_phone,
                ];
            } elseif ($this->type === 'service') {
                $donationData['details'] = [
                    'service_type' => $this->service_type,
                    'service_description' => $this->service_description,
                    'availability' => $this->availability,
                ];
            }

            // Create donation
            $donation = Donation::create($donationData);

            // Handle image uploads for materialistic donations
            if ($this->type === 'materialistic' && !empty($this->item_images)) {
                $uploadedImages = [];
                foreach ($this->item_images as $index => $image) {
                    if ($image) {
                        $path = $image->store('donation-images', 'public');
                        $uploadedImages[] = $path;
                    }
                }

                if (!empty($uploadedImages)) {
                    $details = $donation->details;
                    $details['images'] = $uploadedImages;
                    $donation->update(['details' => $details]);
                }
            }

            // Auto-assign to nearest volunteer
            $routingService = app(VolunteerRoutingService::class);
            $assignedVolunteer = $routingService->findNearestHeadVolunteer(
                $this->city_id,
                $this->state_id,
                $this->country_id,
                auth()->id() // Exclude the creator from being assigned
            );

            if ($assignedVolunteer) {
                $donation->update(['assigned_to' => $assignedVolunteer->id]);
            }

            // Send email notification
            try {
                $emailService = app(EmailNotificationService::class);
                $emailService->sendDonationNotification($donation);
            } catch (\Exception $e) {
                // Log error but don't fail the donation submission
                \Log::error('Failed to send donation notification: ' . $e->getMessage());
            }

            $this->submittedDonation = $donation;
            $this->showSuccessMessage = true;

        } catch (\Exception $e) {
            session()->flash('error', 'There was an error submitting your donation. Please try again.');
        }
    }

    public function submitAnother()
    {
        $this->showSuccessMessage = false;
        $this->submittedDonation = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'type', 'country_id', 'state_id', 'city_id', 'pincode', 'address', 'notes',
            'is_urgent', 'amount', 'currency', 'payment_method', 'item_name',
            'item_description', 'alternate_phone', 'service_type', 'service_description',
            'availability', 'item_images'
        ]);
        $this->states = [];
        $this->cities = [];
        $this->item_images = [];
    }

    public function render()
    {
        return view('livewire.volunteer.volunteer-donation-submit');
    }
}
