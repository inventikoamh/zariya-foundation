<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class UpdatePasswordForm extends Component
{
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    protected $messages = [
        'current_password.required' => 'Current password is required.',
        'current_password.current_password' => 'The current password is incorrect.',
        'password.required' => 'New password is required.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], $this->messages);

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('password-updated', 'Your password has been updated.');
    }

    public function render()
    {
        return view('livewire.profile.update-password-form');
    }
}
