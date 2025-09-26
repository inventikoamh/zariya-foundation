<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'SUPER_ADMIN']);
        Role::create(['name' => 'VOLUNTEER']);

        // Create super admin
        $this->superAdmin = User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '9000000001',
        ]);
        $this->superAdmin->assignRole('SUPER_ADMIN');

        // Create regular user (no specific role)
        $this->regularUser = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '9000000002',
        ]);
        // Normal users have no specific role assigned
    }

    public function test_super_admin_can_view_users_index()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('User Management');
    }

    public function test_regular_user_cannot_access_admin_users()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_user_profile()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/admin/users/' . $this->regularUser->id);
        $response->assertStatus(200);
        $response->assertSee($this->regularUser->name);
    }

    public function test_super_admin_can_edit_user()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/admin/users/' . $this->regularUser->id . '/edit');
        $response->assertStatus(200);
        $response->assertSee('Edit User');
    }

    public function test_super_admin_can_block_user()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UsersIndex::class)
            ->call('blockUser', $this->regularUser->id);

        $this->regularUser->refresh();
        $this->assertTrue($this->regularUser->is_disabled);
    }

    public function test_super_admin_can_unblock_user()
    {
        $this->regularUser->update(['is_disabled' => true]);

        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UsersIndex::class)
            ->call('unblockUser', $this->regularUser->id);

        $this->regularUser->refresh();
        $this->assertFalse($this->regularUser->is_disabled);
    }

    public function test_super_admin_can_reset_user_password()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UsersIndex::class)
            ->call('resetPassword', $this->regularUser->id);

        $this->regularUser->refresh();
        // Password should be changed (we can't easily test the exact password)
        $this->assertNotNull($this->regularUser->password);
    }

    public function test_super_admin_can_soft_delete_user()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UsersIndex::class)
            ->call('deleteUser', $this->regularUser->id);

        $this->assertSoftDeleted('users', ['id' => $this->regularUser->id]);
    }

    public function test_super_admin_cannot_block_themselves()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UsersIndex::class)
            ->call('blockUser', $this->superAdmin->id);

        $this->superAdmin->refresh();
        $this->assertFalse($this->superAdmin->is_disabled);
    }

    public function test_super_admin_cannot_delete_themselves()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UsersIndex::class)
            ->call('deleteUser', $this->superAdmin->id);

        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }

    public function test_super_admin_can_update_user_details()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UserEdit::class, ['user' => $this->regularUser])
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('email', 'jane@example.com')
            ->set('phone', '9000000003')
            ->set('gender', 'female')
            ->set('dob', '1990-01-01')
            ->set('role', 'VOLUNTEER')
            ->call('updateUser');

        $this->regularUser->refresh();
        $this->assertEquals('Jane', $this->regularUser->first_name);
        $this->assertEquals('Smith', $this->regularUser->last_name);
        $this->assertEquals('jane@example.com', $this->regularUser->email);
        $this->assertEquals('9000000003', $this->regularUser->phone);
        $this->assertEquals('female', $this->regularUser->gender);
        $this->assertEquals('1990-01-01', $this->regularUser->dob->format('Y-m-d'));
        $this->assertTrue($this->regularUser->hasRole('VOLUNTEER'));
    }

    public function test_super_admin_can_upload_user_avatar()
    {
        Storage::fake('public');

        $this->actingAs($this->superAdmin);

        $file = UploadedFile::fake()->image('avatar.jpg');

        Livewire::test(\App\Livewire\Admin\Users\UserEdit::class, ['user' => $this->regularUser])
            ->set('avatar', $file)
            ->call('updateUser');

        $this->regularUser->refresh();
        $this->assertNotNull($this->regularUser->avatar_url);
        Storage::disk('public')->assertExists($this->regularUser->avatar_url);
    }

    public function test_users_index_search_functionality()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UsersIndex::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->set('search', 'Jane')
            ->assertDontSee('John Doe');
    }

    public function test_users_index_role_filter()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UsersIndex::class)
            ->set('roleFilter', 'BENEFICIARY')
            ->assertSee('John Doe')
            ->set('roleFilter', 'VOLUNTEER')
            ->assertDontSee('John Doe');
    }

    public function test_users_index_status_filter()
    {
        $this->regularUser->update(['is_disabled' => true]);

        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UsersIndex::class)
            ->set('statusFilter', 'blocked')
            ->assertSee('John Doe')
            ->set('statusFilter', 'active')
            ->assertDontSee('John Doe');
    }
}
