<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_information()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'gender' => 'male',
            'phone' => '1234567890',
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Profile\UpdateProfileForm::class)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('email', 'jane@example.com')
            ->set('gender', 'female')
            ->set('dob', '1990-01-01')
            ->set('phone', '0987654321')
            ->set('address_line', '123 Main St')
            ->set('pincode', '123456')
            ->call('updateProfile');

        $user->refresh();

        $this->assertEquals('Jane', $user->first_name);
        $this->assertEquals('Smith', $user->last_name);
        $this->assertEquals('jane@example.com', $user->email);
        $this->assertEquals('female', $user->gender);
        $this->assertEquals('1990-01-01', $user->dob->toDateString());
        $this->assertEquals('0987654321', $user->phone);
        $this->assertEquals('123 Main St', $user->address_line);
        $this->assertEquals('123456', $user->pincode);
    }

    public function test_user_can_upload_profile_image()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
            'dob' => '1990-01-01',
            'phone' => '1234567890',
        ]);
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('profile.jpg');

        Livewire::test(\App\Livewire\Profile\UpdateProfileForm::class)
            ->set('profile_image', $file)
            ->call('updateProfile');

        $user->refresh();

        $this->assertNotNull($user->avatar_url);
        Storage::disk('public')->assertExists($user->avatar_url);
    }

    public function test_user_can_remove_profile_image()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'avatar_url' => 'profile-images/test.jpg'
        ]);
        $this->actingAs($user);

        // Create a fake file
        Storage::disk('public')->put('profile-images/test.jpg', 'fake content');

        Livewire::test(\App\Livewire\Profile\UpdateProfileForm::class)
            ->call('removeProfileImage');

        $user->refresh();

        $this->assertNull($user->avatar_url);
        Storage::disk('public')->assertMissing('profile-images/test.jpg');
    }

    public function test_profile_update_validation_works()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(\App\Livewire\Profile\UpdateProfileForm::class)
            ->set('first_name', '')
            ->set('last_name', '')
            ->set('email', 'invalid-email')
            ->set('gender', 'invalid')
            ->set('phone', '123')
            ->set('pincode', '12345')
            ->call('updateProfile')
            ->assertHasErrors([
                'first_name',
                'last_name', 
                'email',
                'gender',
                'pincode'
            ]);
    }
}
