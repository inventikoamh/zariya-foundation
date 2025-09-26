<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class UsersIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function blockUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent blocking self
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot block yourself.');
            return;
        }

        $user->update(['is_disabled' => true]);
        session()->flash('success', "User {$user->name} has been blocked.");
    }

    public function unblockUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_disabled' => false]);
        session()->flash('success', "User {$user->name} has been unblocked.");
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete yourself.');
            return;
        }

        // Prevent deleting last Super Admin
        if ($user->hasRole('SUPER_ADMIN')) {
            $superAdminCount = User::role('SUPER_ADMIN')->count();
            if ($superAdminCount <= 1) {
                session()->flash('error', 'Cannot delete the last Super Admin.');
                return;
            }
        }

        $user->delete();
        session()->flash('success', "User {$user->name} has been deleted.");
    }

    public function resetPassword($userId)
    {
        $user = User::findOrFail($userId);
        
        // Generate random password
        $newPassword = $this->generateRandomPassword();
        
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        session()->flash('success', "Password reset for {$user->name}. New password: {$newPassword}");
    }

    private function generateRandomPassword($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
    }

    public function render()
    {
        $query = User::query();

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%');
            });
        }

        // Apply role filter
        if ($this->roleFilter) {
            $query->role($this->roleFilter);
        }

        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('is_disabled', false);
        } elseif ($this->statusFilter === 'blocked') {
            $query->where('is_disabled', true);
        }

        $users = $query->with('roles')->paginate($this->perPage);
        $roles = Role::all();

        return view('livewire.admin.users.users-index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}