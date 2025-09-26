<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class DeleteUserForm extends Component
{
    public $password = '';
    public $confirmingUserDeletion = false;

    protected $rules = [
        'password' => ['required', 'current_password'],
    ];

    protected $messages = [
        'password.required' => 'Password is required.',
        'password.current_password' => 'The password is incorrect.',
    ];

    public function confirmUserDeletion()
    {
        $this->resetErrorBag('password');
        $this->password = '';
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser()
    {
        $this->validate();

        $user = Auth::user();

        // Delete user's donations first
        $user->donations()->delete();
        
        // Delete user's remarks
        $user->remarks()->delete();
        
        // Delete user's volunteer assignments
        $user->volunteerAssignments()->delete();

        // Delete the user account
        $user->delete();

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.profile.delete-user-form');
    }
}
