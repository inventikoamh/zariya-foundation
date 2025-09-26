<?php

namespace App\Livewire\Admin\Donations;

use App\Models\Donation;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Services\StatusHelper;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class DonationEdit extends Component
{
    use WithFileUploads;

    public Donation $donation;

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

    public $status = '';

    // assigned_to is now read-only, managed from donation view page

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

    public function mount(Donation $donation)
    {
        // Check if current user is the creator of the donation
        if ($donation->donor_id !== auth()->id()) {
            abort(403, 'You can only edit donations that you created.');
        }

        $this->donation = $donation;

        // Load basic fields
        $this->type = $donation->type;
        $this->donor_id = $donation->donor_id;
        $this->country_id = $donation->country_id;
        $this->state_id = $donation->state_id;
        $this->city_id = $donation->city_id;
        $this->pincode = $donation->pincode;
        $this->address = $donation->address;
        $this->notes = $donation->notes;
        $this->is_urgent = $donation->is_urgent;
        $this->priority = $donation->priority;
        $this->status = $donation->status;

        // Load type-specific fields
        $details = $donation->details;
        if ($donation->type === 'monetary') {
            $this->amount = $details['amount'] ?? '';
            $this->currency = $details['currency'] ?? 'USD';
            $this->payment_method = $details['payment_method'] ?? '';
        } elseif ($donation->type === 'materialistic') {
            $this->item_name = $details['item_name'] ?? '';
            $this->item_description = $details['item_description'] ?? '';
            $this->alternate_phone = $details['alternate_phone'] ?? '';
        } elseif ($donation->type === 'service') {
            $this->service_type = $details['service_type'] ?? '';
            $this->service_description = $details['service_description'] ?? '';
            $this->availability = $details['availability'] ?? '';
        }

        // Load related data
        $this->countries = Country::where('is_active', true)->orderBy('name')->get();
        $this->donors = User::whereDoesntHave('roles')->orderBy('first_name')->get();

        if ($this->country_id) {
            $this->states = State::where('country_id', $this->country_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        if ($this->state_id) {
            $this->cities = City::where('state_id', $this->state_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }
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
        $this->service_type = '';
        $this->service_description = '';
        $this->availability = '';
    }

    public function rules()
    {
        $statusOptions = StatusHelper::getStatusOptions($this->type);
        $statusValues = implode(',', array_keys($statusOptions));

        return [
            'type' => 'required|in:monetary,materialistic,service',
            'donor_id' => 'required|exists:users,id',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'pincode' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'is_urgent' => 'boolean',
            'priority' => 'required|integer|min:1|max:4',
            'status' => "required|in:{$statusValues}",
            'amount' => 'required_if:type,monetary|numeric|min:0',
            'currency' => 'required_if:type,monetary|string|max:10',
            'payment_method' => 'required_if:type,monetary|string|max:50',
            'item_name' => 'required_if:type,materialistic|string|max:100',
            'item_description' => 'required_if:type,materialistic|string|max:1000',
            'alternate_phone' => 'required_if:type,materialistic|string|max:20',
            'service_type' => 'required_if:type,service|string|max:100',
            'service_description' => 'required_if:type,service|string|max:1000',
            'availability' => 'required_if:type,service|string|max:100',
        ];
    }

    public function save()
    {
        // Double-check authorization before saving
        if ($this->donation->donor_id !== auth()->id()) {
            abort(403, 'You can only edit donations that you created.');
        }

        $this->validate();

        // Prepare details based on donation type
        $details = [];

        if ($this->type === 'monetary') {
            $details = [
                'amount' => $this->amount,
                'currency' => $this->currency,
                'payment_method' => $this->payment_method,
            ];
        } elseif ($this->type === 'materialistic') {
            $details = [
                'item_name' => $this->item_name,
                'item_description' => $this->item_description,
                'alternate_phone' => $this->alternate_phone,
                'images' => $this->donation->details['images'] ?? [], // Preserve existing images
            ];
        } elseif ($this->type === 'service') {
            $details = [
                'service_type' => $this->service_type,
                'service_description' => $this->service_description,
                'availability' => $this->availability,
            ];
        }

        // Update the donation
        $this->donation->update([
            'type' => $this->type,
            'details' => $details,
            'donor_id' => $this->donor_id,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'pincode' => $this->pincode,
            'address' => $this->address,
            'notes' => $this->notes,
            'is_urgent' => $this->is_urgent,
            'priority' => $this->priority,
            'status' => $this->status,
        ]);

        // Add update remark
        $this->donation->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'general',
            'remark' => 'Donation details updated',
        ]);

        session()->flash('success', 'Donation updated successfully.');

        return redirect()->route('admin.donations.show', $this->donation);
    }

    public function render()
    {
        return view('livewire.admin.donations.donation-edit');
    }
}
