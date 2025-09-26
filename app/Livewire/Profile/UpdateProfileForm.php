<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileForm extends Component
{
    use WithFileUploads;

    // Basic Information
    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('nullable|email|max:255')]
    public string $email = '';

    // Personal Information
    #[Validate('required|in:male,female,other')]
    public string $gender = '';

    #[Validate('required|date|before:today')]
    public string $dob = '';

    // Contact Information
    public string $phone = '';

    // Address Information
    #[Validate('nullable|string|max:500')]
    public string $address_line = '';

    #[Validate('nullable|digits:6')]
    public string $pincode = '';

    // Profile Image
    public $profile_image;
    public $current_avatar_url = '';

    public function mount()
    {
        $user = Auth::user();

        $this->first_name = $user->first_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->email = $user->email ?? '';
        $this->gender = $user->gender ?? '';
        $this->dob = $user->dob ? $user->dob->toDateString() : '';
        $this->phone = $user->phone ?? '';
        $this->address_line = $user->address_line ?? '';
        $this->pincode = $user->pincode ?? '';
        $this->current_avatar_url = $user->avatar_url ?? '';
    }

    public function updateProfile()
    {
        $user = Auth::user();

        // Validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:today',
            'phone' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($user->id)],
            'address_line' => 'nullable|string|max:500',
            'pincode' => 'nullable|digits:6',
        ];

        // Add profile image validation if uploaded
        if ($this->profile_image) {
            $rules['profile_image'] = 'image|max:2048|mimes:jpeg,png,jpg,gif';
        }

        $validated = $this->validate($rules);

        try {
            // Handle profile image upload
            if ($this->profile_image) {
                // Delete old profile image if exists
                if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                    Storage::disk('public')->delete($user->avatar_url);
                }

                // Store new profile image
                $imagePath = $this->profile_image->store('profile-images', 'public');
                $validated['avatar_url'] = $imagePath;
            }

            // Update user data
            $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

            $user->update($validated);

            // Reset profile image property and update current avatar URL
            $this->profile_image = null;
            $this->current_avatar_url = $user->fresh()->avatar_url ?? '';

            session()->flash('success', 'Profile updated successfully!');

            $this->dispatch('profile-updated', name: $user->name);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update profile. Please try again.');
        }
    }

    public function removeProfileImage()
    {
        $user = Auth::user();

        if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        $user->update(['avatar_url' => null]);
        $this->current_avatar_url = '';

        session()->flash('success', 'Profile image removed successfully!');
    }

    public function render()
    {
        return view('livewire.profile.update-profile-form');
    }
}
