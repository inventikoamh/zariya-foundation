<?php

namespace App\Livewire\Admin\Donations;

use App\Models\Donation;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Services\VolunteerRoutingService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class DonationCreate extends Component
{
    use WithFileUploads;

    // Basic donation fields
    #[Validate('required|in:monetary,materialistic,service')]
    public $type = '';

    #[Validate('required|exists:users,id')]
    public $donor_id = '';

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

    #[Validate('required|integer|min:1|max:4')]
    public $priority = 1;

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

    #[Validate('required_if:type,materialistic|array|min:1|max:5')]
    public $item_images = [];

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
    public $donors = [];

    public function mount()
    {
        $this->countries = Country::where('is_active', true)->orderBy('name')->get();
        $this->donors = User::orderBy('first_name')->get();
        $this->item_images = [null]; // Initialize with one empty slot
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
        $this->currency = 'USD';
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
        unset($this->item_images[$index]);
        $this->item_images = array_values($this->item_images); // Re-index array
    }

    public function save()
    {
        // Custom validation for materialistic images
        if ($this->type === 'materialistic') {
            $this->validate([
                'item_images' => 'required|array|min:1|max:5',
                'item_images.*' => 'image|max:5120', // 5MB = 5120KB
            ], [
                'item_images.required' => 'At least one image is required for materialistic donations.',
                'item_images.min' => 'At least one image is required for materialistic donations.',
                'item_images.max' => 'Maximum 5 images allowed for materialistic donations.',
                'item_images.*.image' => 'Each file must be an image.',
                'item_images.*.max' => 'Each image must be less than 5MB.',
            ]);
        }

        $this->validate();

        // Create the donation with type-specific fields
        $donationData = [
            'type' => $this->type,
            'donor_id' => $this->donor_id,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'pincode' => $this->pincode,
            'address' => $this->address,
            'notes' => $this->notes,
            'is_urgent' => $this->is_urgent,
            'priority' => $this->priority,
            'status' => 'pending',
        ];

        // Add type-specific fields
        if ($this->type === 'monetary') {
            $donationData['monetary_amount'] = $this->amount;
            $donationData['payment_method'] = $this->payment_method;
        } elseif ($this->type === 'materialistic') {
            // Handle image uploads
            $imagePaths = [];
            if ($this->item_images) {
                foreach ($this->item_images as $image) {
                    $path = $image->store('donations/images', 'public');
                    $imagePaths[] = $path;
                }
            }

            $donationData['materialistic_item'] = $this->item_name;
            $donationData['materialistic_description'] = $this->item_description;
            $donationData['materialistic_alt_phone'] = $this->alternate_phone;
            $donationData['materialistic_images'] = $imagePaths;
        } elseif ($this->type === 'service') {
            $donationData['service_type'] = $this->service_type;
            $donationData['service_description'] = $this->service_description;
            $donationData['service_availability'] = [$this->availability]; // Store as array
        }

        // Create the donation
        $donation = Donation::create($donationData);

        // Auto-route to appropriate head volunteer (exclude the donor)
        $routingService = app(VolunteerRoutingService::class);
        $assignedVolunteer = $routingService->findNearestHeadVolunteer(
            $this->city_id,
            $this->state_id,
            $this->country_id,
            $this->donor_id // Exclude the donor from being assigned
        );

        if ($assignedVolunteer) {
            $donation->update([
                'assigned_to' => $assignedVolunteer->id,
                'status' => 'assigned',
            ]);

            // Add assignment remark
            $donation->remarks()->create([
                'user_id' => auth()->id(),
                'type' => 'assignment',
                'remark' => "Auto-assigned to {$assignedVolunteer->first_name} {$assignedVolunteer->last_name}",
            ]);
        }

        session()->flash('success', 'Donation created successfully and auto-routed to appropriate volunteer.');

        return redirect()->route('admin.donations.show', $donation);
    }

    public function render()
    {
        return view('livewire.admin.donations.donation-create');
    }
}
