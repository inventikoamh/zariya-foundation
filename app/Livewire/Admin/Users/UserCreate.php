<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class UserCreate extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:50')]
    public string $first_name = '';

    #[Validate('nullable|string|max:50')]
    public string $last_name = '';

    #[Validate('nullable|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|regex:/^[6-9][0-9]{9}$/|unique:users,phone')]
    public string $phone = '';

    #[Validate('nullable|string|max:500')]
    public string $address_line = '';

    #[Validate('nullable|digits:6')]
    public string $pincode = '';

    #[Validate('required|in:male,female,other')]
    public string $gender = '';

    #[Validate('required|date|before:today')]
    public string $dob = '';

    #[Validate('required|exists:roles,name')]
    public string $role = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    #[Validate('required|string|min:8|same:password')]
    public string $password_confirmation = '';

    public $avatar;

    public function createUser()
    {
        // Custom validation rules
        $rules = [
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'required|regex:/^[6-9][0-9]{9}$/|unique:users,phone',
            'address_line' => 'nullable|string|max:500',
            'pincode' => 'nullable|digits:6',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:today',
            'role' => 'required|exists:roles,name',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password',
        ];

        // Add avatar validation if uploaded
        if ($this->avatar) {
            $rules['avatar'] = 'image|max:5120';
        }

        $validated = $this->validate($rules);

        try {
            // Handle avatar upload
            $avatarPath = null;
            if ($this->avatar) {
                $avatarPath = $this->avatar->store('profile-images', 'public');
            }

            // Create user data
            $userData = [
                'name' => $validated['first_name'] . ' ' . ($validated['last_name'] ?? ''),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'] ?: null,
                'phone' => $validated['phone'],
                'phone_country_code' => '+91',
                'phone_verified_at' => now(),
                'password' => Hash::make($validated['password']),
                'gender' => $validated['gender'],
                'dob' => $validated['dob'],
                'address_line' => $validated['address_line'],
                'pincode' => $validated['pincode'],
                'avatar_url' => $avatarPath,
            ];

            $user = User::create($userData);

            // Assign role
            $user->assignRole($validated['role']);

            session()->flash('success', 'User created successfully!');
            
            // Redirect to user show page
            return $this->redirect(route('admin.users.show', $user), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create user. Please try again.');
        }
    }

    public function render()
    {
        $roles = Role::all();
        
        return view('livewire.admin.users.user-create', [
            'roles' => $roles,
        ]);
    }
}