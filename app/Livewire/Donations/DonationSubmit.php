<?php

namespace App\Livewire\Donations;

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

#[Layout('layouts.app')]
class DonationSubmit extends Component
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
        unset($this->item_images[$index]);
        $this->item_images = array_values($this->item_images); // Re-index array

        // Clear the corresponding image property
        $imageProperty = 'image_' . ($index + 1);
        if (property_exists($this, $imageProperty)) {
            $this->$imageProperty = null;
        }
    }

    public function getUploadedImages()
    {
        $images = [];
        for ($i = 1; $i <= 5; $i++) {
            $property = "image_{$i}";
            if ($this->$property) {
                $images[] = $this->$property;
            }
        }
        return $images;
    }

    public function submitDonation()
    {
        try {
            // Custom validation for materialistic images
            if ($this->type === 'materialistic') {
                $uploadedImages = $this->getUploadedImages();

                if (empty($uploadedImages)) {
                    $this->addError('item_images', 'At least one image is required for materialistic donations.');
                    return;
                }

                // Validate each uploaded image
                foreach ($uploadedImages as $index => $image) {
                    if ($image) {
                        if (!$image->isValid()) {
                            $this->addError("image_" . ($index + 1), 'The uploaded file is not valid.');
                            return;
                        }

                        if (!$image->getMimeType() || !str_starts_with($image->getMimeType(), 'image/')) {
                            $this->addError("image_" . ($index + 1), 'The file must be an image.');
                            return;
                        }

                        if ($image->getSize() > 5120 * 1024) { // 5MB in bytes
                            $this->addError("image_" . ($index + 1), 'The image must be less than 5MB.');
                            return;
                        }
                    }
                }
            }

            $this->validate();

        // Check if user is authenticated
        if (!auth()->check()) {
            session()->flash('error', 'Please login to submit a donation.');
            return;
        }


        // Prepare details based on donation type
        $details = [];

        if ($this->type === 'monetary') {
            $details = [
                'amount' => $this->amount,
                'currency' => $this->currency,
                'payment_method' => $this->payment_method,
            ];
        } elseif ($this->type === 'materialistic') {
            // Handle image uploads
            $imagePaths = [];
            $uploadedImages = $this->getUploadedImages();
            foreach ($uploadedImages as $image) {
                if ($image && $image !== null) {
                    $path = $image->store('donations/images', 'public');
                    $imagePaths[] = $path;
                }
            }

            $details = [
                'item_name' => $this->item_name,
                'item_description' => $this->item_description,
                'alternate_phone' => $this->alternate_phone,
                'images' => $imagePaths,
            ];
        } elseif ($this->type === 'service') {
            $details = [
                'service_type' => $this->service_type,
                'service_description' => $this->service_description,
                'availability' => $this->availability,
            ];
        }

        // Create the donation
        $donationData = [
            'type' => $this->type,
            'details' => $details,
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

        // Create the donation
        $donation = Donation::create($donationData);

        // Auto-route to appropriate head volunteer (exclude the creator)
        $routingService = app(VolunteerRoutingService::class);
        $assignedVolunteer = $routingService->findNearestHeadVolunteer(
            $this->city_id,
            $this->state_id,
            $this->country_id,
            auth()->id() // Exclude the creator from being assigned
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

        // Add initial remark
        $donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'general',
            'remark' => 'Donation submitted successfully',
        ]);

        // Send donation confirmation email
        try {
            $emailService = app(EmailNotificationService::class);
            $emailService->sendDonationConfirmation(auth()->user(), $donation);
        } catch (\Exception $e) {
            // Log email error but don't fail the donation submission
            \Log::error('Failed to send donation confirmation email: ' . $e->getMessage());
        }

            $this->submittedDonation = $donation;
            $this->showSuccessMessage = true;

            // Reset form
            $this->resetForm();

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while submitting the donation: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->type = '';
        $this->country_id = '';
        $this->state_id = '';
        $this->city_id = '';
        $this->pincode = '';
        $this->address = '';
        $this->notes = '';
        $this->is_urgent = false;
        $this->priority = 1;
        $this->amount = '';
        $this->currency = SystemSetting::get('currency', 'USD');
        $this->payment_method = '';
        $this->item_name = '';
        $this->item_description = '';
        $this->alternate_phone = '';
        $this->item_images = [];
        $this->service_type = '';
        $this->service_description = '';
        $this->availability = '';
        $this->states = [];
        $this->cities = [];
    }

    public function submitAnother()
    {
        $this->showSuccessMessage = false;
        $this->submittedDonation = null;
    }

    public function getPriorityOptions()
    {
        return [
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            4 => 'Critical',
        ];
    }

    public function getCurrencyOptions()
    {
        return [
            'INR' => 'Indian Rupee (₹)',
            'USD' => 'US Dollar ($)',
            'EUR' => 'Euro (€)',
        ];
    }

    public function getPaymentMethodOptions()
    {
        return [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'other' => 'Other',
        ];
    }

    public function getServiceTypeOptions()
    {
        return [
            'education' => 'Education/Tutoring',
            'healthcare' => 'Healthcare/Medical',
            'transportation' => 'Transportation',
            'food_service' => 'Food Service',
            'cleaning' => 'Cleaning/Maintenance',
            'technology' => 'Technology Support',
            'counseling' => 'Counseling/Therapy',
            'legal' => 'Legal Assistance',
            'other' => 'Other',
        ];
    }

    public function render()
    {
        return view('livewire.donations.donation-submit');
    }

}
