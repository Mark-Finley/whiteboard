<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_access_control_page(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get('/admin/access-control')
            ->assertOk()
            ->assertSee('Access Control', false)
            ->assertSee('Save access', false);
    }

    public function test_admin_can_update_user_role_from_access_control_page(): void
    {
        $admin = $this->makeAdmin();
        $wardRole = Role::query()->where('name', 'Ward Staff')->firstOrFail();
        $triageRole = Role::query()->where('name', 'Triage Nurse')->firstOrFail();
        $team = Team::query()->where('name', 'Emergency Medicine')->firstOrFail();
        $user = User::query()->create([
            'name' => 'Ward Member',
            'email' => 'ward.member@kath.gov.gh',
            'phone' => '233000000050',
            'password' => 'Password123!',
            'role_id' => $wardRole->id,
            'team_id' => $team->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->put('/users/'.$user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'password' => '',
            'password_confirmation' => '',
            'role_id' => $triageRole->id,
            'team_id' => $team->id,
            'status' => 'inactive',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role_id' => $triageRole->id,
            'status' => 'inactive',
        ]);
    }

    private function makeAdmin(): User
    {
        $adminRole = Role::query()->create(['name' => 'Admin']);
        Role::query()->create(['name' => 'Triage Nurse']);
        Role::query()->create(['name' => 'Ward Staff']);
        Team::query()->create(['name' => 'Emergency Medicine']);

        return User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@kath.gov.gh',
            'phone' => '233000000000',
            'password' => 'Password123!',
            'role_id' => $adminRole->id,
            'team_id' => Team::query()->first()->id,
            'status' => 'active',
        ]);
    }
}
