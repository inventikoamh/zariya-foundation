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

class AdminUserCreateTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

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
    }

    public function test_super_admin_can_view_create_user_page()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/admin/users/create');
        $response->assertStatus(200);
        $response->assertSee('Create New User');
    }

    public function test_regular_user_cannot_access_create_user_page()
    {
        $regularUser = User::factory()->create();
        // Normal users have no specific role assigned
        
        $this->actingAs($regularUser);

        $response = $this->get('/admin/users/create');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_create_user_with_all_fields()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UserCreate::class)
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('phone', '9000000002')
            ->set('gender', 'male')
            ->set('dob', '1990-01-01')
            ->set('address_line', '123 Main St')
            ->set('pincode', '123456')
            ->set('role', 'VOLUNTEER')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('createUser');

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '9000000002',
            'gender' => 'male',
            'address_line' => '123 Main St',
            'pincode' => '123456',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue($user->hasRole('VOLUNTEER'));
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_super_admin_can_create_user_with_avatar()
    {
        Storage::fake('public');

        $this->actingAs($this->superAdmin);

        $file = UploadedFile::fake()->image('avatar.jpg');

        Livewire::test(\App\Livewire\Admin\Users\UserCreate::class)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('phone', '9000000003')
            ->set('gender', 'female')
            ->set('dob', '1990-01-01')
            ->set('role', 'DONOR')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('avatar', $file)
            ->call('createUser');

        $user = User::where('phone', '9000000003')->first();
        $this->assertNotNull($user->avatar_url);
        Storage::disk('public')->assertExists($user->avatar_url);
    }

    public function test_user_creation_validation_works()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UserCreate::class)
            ->set('first_name', '')
            ->set('phone', '123')
            ->set('gender', 'invalid')
            ->set('role', 'INVALID_ROLE')
            ->set('password', '123')
            ->set('password_confirmation', '456')
            ->call('createUser')
            ->assertHasErrors([
                'first_name',
                'phone',
                'gender',
                'role',
                'password',
                'password_confirmation'
            ]);
    }

    public function test_cannot_create_user_with_duplicate_phone()
    {
        // Create existing user
        User::factory()->create(['phone' => '9000000004']);

        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UserCreate::class)
            ->set('first_name', 'Test')
            ->set('phone', '9000000004')
            ->set('gender', 'male')
            ->set('dob', '1990-01-01')
            ->set('role', 'BENEFICIARY')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('createUser')
            ->assertHasErrors(['phone']);
    }

    public function test_cannot_create_user_with_duplicate_email()
    {
        // Create existing user
        User::factory()->create(['email' => 'test@example.com']);

        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UserCreate::class)
            ->set('first_name', 'Test')
            ->set('email', 'test@example.com')
            ->set('phone', '9000000005')
            ->set('gender', 'male')
            ->set('dob', '1990-01-01')
            ->set('role', 'BENEFICIARY')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('createUser')
            ->assertHasErrors(['email']);
    }

    public function test_created_user_has_phone_verified()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(\App\Livewire\Admin\Users\UserCreate::class)
            ->set('first_name', 'Test')
            ->set('phone', '9000000006')
            ->set('gender', 'male')
            ->set('dob', '1990-01-01')
            ->set('role', 'BENEFICIARY')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('createUser');

        $user = User::where('phone', '9000000006')->first();
        $this->assertNotNull($user->phone_verified_at);
        $this->assertEquals('+91', $user->phone_country_code);
    }
}
