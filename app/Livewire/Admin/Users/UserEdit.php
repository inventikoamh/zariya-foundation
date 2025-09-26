<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class UserEdit extends Component
{
    use WithFileUploads;

    public User $user;

    #[Validate('required|string|max:50')]
    public string $first_name = '';

    #[Validate('nullable|string|max:50')]
    public string $last_name = '';

    #[Validate('nullable|email|max:255')]
    public string $email = '';

    #[Validate('required|regex:/^[6-9][0-9]{9}$/')]
    public string $phone = '';

    #[Validate('nullable|string|max:500')]
    public string $address_line = '';

    #[Validate('nullable|digits:6')]
    public string $pincode = '';

    #[Validate('required|in:male,female,other')]
    public string $gender = '';

    #[Validate('required|date|before:today')]
    public string $dob = '';

    public string $role = '';
    public $avatar;
    public $current_avatar_url = '';

    public function mount(User $user)
    {
        $this->user = $user;
        $this->first_name = $user->first_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
        $this->address_line = $user->address_line ?? '';
        $this->pincode = $user->pincode ?? '';
        $this->gender = $user->gender ?? '';
        $this->dob = $user->dob ? $user->dob->format('Y-m-d') : '';
        $this->role = $user->roles->first()?->name ?? '';
        $this->current_avatar_url = $user->avatar_url ?? '';
    }

    public function updateUser()
    {
        // Custom validation rules
        $rules = [
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'phone' => ['required', 'regex:/^[6-9][0-9]{9}$/', 'unique:users,phone,' . $this->user->id],
            'address_line' => 'nullable|string|max:500',
            'pincode' => 'nullable|digits:6',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:today',
            'role' => 'required|exists:roles,name',
        ];

        // Add avatar validation if uploaded
        if ($this->avatar) {
            $rules['avatar'] = 'image|max:5120';
        }

        $validated = $this->validate($rules);

        try {
            // Handle avatar upload
            if ($this->avatar) {
                // Delete old avatar if exists
                if ($this->user->avatar_url && Storage::disk('public')->exists($this->user->avatar_url)) {
                    Storage::disk('public')->delete($this->user->avatar_url);
                }

                // Store new avatar
                $avatarPath = $this->avatar->store('profile-images', 'public');
                $validated['avatar_url'] = $avatarPath;
            }

            // Update user data
            $validated['name'] = $validated['first_name'] . ' ' . ($validated['last_name'] ?? '');

            $this->user->update($validated);

            // Update role
            $this->user->syncRoles([$validated['role']]);

            // Reset avatar property
            $this->avatar = null;
            $this->current_avatar_url = $this->user->fresh()->avatar_url ?? '';

            session()->flash('success', 'User updated successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update user. Please try again.');
        }
    }

    public function render()
    {
        $roles = Role::all();

        return view('livewire.admin.users.user-edit', [
            'roles' => $roles,
        ]);
    }
}
