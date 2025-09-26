<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\VolunteerAssignment;
use App\Services\VolunteerRoutingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Admin\Localization\VolunteerAssignmentManage;
use Tests\TestCase;

class VolunteerAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->country = Country::create([
            'name' => 'Test Country',
            'code' => 'TC',
            'phone_code' => '+1',
            'is_active' => true,
        ]);

        $this->state = State::create([
            'name' => 'Test State',
            'code' => 'TS',
            'country_id' => $this->country->id,
            'is_active' => true,
        ]);

        $this->city = City::create([
            'name' => 'Test City',
            'pincode' => '12345',
            'state_id' => $this->state->id,
            'country_id' => $this->country->id,
            'is_active' => true,
        ]);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('SUPER_ADMIN');

        $this->volunteer = User::factory()->create();
        $this->volunteer->assignRole('VOLUNTEER');
    }

    public function test_super_admin_can_view_volunteer_assignment_page()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('admin.localization.volunteers'));

        $response->assertStatus(200);
        $response->assertSee('Volunteer Assignment Management');
    }

    public function test_volunteer_cannot_access_volunteer_assignment_page()
    {
        $this->actingAs($this->volunteer);

        $response = $this->get(route('admin.localization.volunteers'));

        $response->assertStatus(403);
    }

    public function test_can_create_volunteer_assignment()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(VolunteerAssignmentManage::class)
            ->call('openModal')
            ->set('user_id', $this->volunteer->id)
            ->set('assignment_type', 'city')
            ->set('role', 'head_volunteer')
            ->set('country_id', $this->country->id)
            ->set('state_id', $this->state->id)
            ->set('city_id', $this->city->id)
            ->set('notes', 'Test assignment')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Volunteer assignment created successfully');

        $this->assertDatabaseHas('volunteer_assignments', [
            'user_id' => $this->volunteer->id,
            'assignment_type' => 'city',
            'role' => 'head_volunteer',
            'city_id' => $this->city->id,
            'notes' => 'Test assignment',
        ]);
    }

    public function test_cannot_assign_multiple_head_volunteers_to_same_region()
    {
        $this->actingAs($this->superAdmin);

        // Create first head volunteer
        VolunteerAssignment::create([
            'user_id' => $this->volunteer->id,
            'assignment_type' => 'city',
            'role' => 'head_volunteer',
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'is_active' => true,
        ]);

        // Try to create second head volunteer for same region
        $anotherVolunteer = User::factory()->create();
        $anotherVolunteer->assignRole('VOLUNTEER');

        Livewire::test(VolunteerAssignmentManage::class)
            ->call('openModal')
            ->set('user_id', $anotherVolunteer->id)
            ->set('assignment_type', 'city')
            ->set('role', 'head_volunteer')
            ->set('country_id', $this->country->id)
            ->set('state_id', $this->state->id)
            ->set('city_id', $this->city->id)
            ->call('save')
            ->assertHasErrors(['role']);
    }

    public function test_volunteer_routing_service_finds_nearest_head_volunteer()
    {
        // Create head volunteer for city
        VolunteerAssignment::create([
            'user_id' => $this->volunteer->id,
            'assignment_type' => 'city',
            'role' => 'head_volunteer',
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'is_active' => true,
        ]);

        $routingService = new VolunteerRoutingService();
        $headVolunteer = $routingService->findNearestHeadVolunteer(
            $this->city->id,
            $this->state->id,
            $this->country->id
        );

        $this->assertNotNull($headVolunteer);
        $this->assertEquals($this->volunteer->id, $headVolunteer->id);
    }

    public function test_volunteer_routing_service_falls_back_to_state_head_volunteer()
    {
        // Create head volunteer for state (not city)
        VolunteerAssignment::create([
            'user_id' => $this->volunteer->id,
            'assignment_type' => 'state',
            'role' => 'head_volunteer',
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => null,
            'is_active' => true,
        ]);

        $routingService = new VolunteerRoutingService();
        $headVolunteer = $routingService->findNearestHeadVolunteer(
            $this->city->id,
            $this->state->id,
            $this->country->id
        );

        $this->assertNotNull($headVolunteer);
        $this->assertEquals($this->volunteer->id, $headVolunteer->id);
    }

    public function test_volunteer_routing_service_falls_back_to_country_head_volunteer()
    {
        // Create head volunteer for country (not state or city)
        VolunteerAssignment::create([
            'user_id' => $this->volunteer->id,
            'assignment_type' => 'country',
            'role' => 'head_volunteer',
            'country_id' => $this->country->id,
            'state_id' => null,
            'city_id' => null,
            'is_active' => true,
        ]);

        $routingService = new VolunteerRoutingService();
        $headVolunteer = $routingService->findNearestHeadVolunteer(
            $this->city->id,
            $this->state->id,
            $this->country->id
        );

        $this->assertNotNull($headVolunteer);
        $this->assertEquals($this->volunteer->id, $headVolunteer->id);
    }

    public function test_can_edit_volunteer_assignment()
    {
        $this->actingAs($this->superAdmin);

        $assignment = VolunteerAssignment::create([
            'user_id' => $this->volunteer->id,
            'assignment_type' => 'city',
            'role' => 'volunteer',
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'is_active' => true,
        ]);

        Livewire::test(VolunteerAssignmentManage::class)
            ->call('openModal', $assignment->id)
            ->set('role', 'head_volunteer')
            ->set('notes', 'Updated assignment')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Volunteer assignment updated successfully');

        $assignment->refresh();
        $this->assertEquals('head_volunteer', $assignment->role);
        $this->assertEquals('Updated assignment', $assignment->notes);
    }

    public function test_can_delete_volunteer_assignment()
    {
        $this->actingAs($this->superAdmin);

        $assignment = VolunteerAssignment::create([
            'user_id' => $this->volunteer->id,
            'assignment_type' => 'city',
            'role' => 'volunteer',
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'is_active' => true,
        ]);

        Livewire::test(VolunteerAssignmentManage::class)
            ->call('delete', $assignment->id)
            ->assertSee('Volunteer assignment deleted successfully');

        $this->assertDatabaseMissing('volunteer_assignments', [
            'id' => $assignment->id,
        ]);
    }

    public function test_can_toggle_volunteer_assignment_status()
    {
        $this->actingAs($this->superAdmin);

        $assignment = VolunteerAssignment::create([
            'user_id' => $this->volunteer->id,
            'assignment_type' => 'city',
            'role' => 'volunteer',
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'is_active' => true,
        ]);

        Livewire::test(VolunteerAssignmentManage::class)
            ->call('toggleStatus', $assignment->id)
            ->assertSee('Volunteer assignment status updated successfully');

        $assignment->refresh();
        $this->assertFalse($assignment->is_active);
    }
}
