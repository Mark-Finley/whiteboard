<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_user_to_role_dashboard(): void
    {
        $role = Role::query()->create(['name' => 'Admin']);
        $team = Team::query()->create(['name' => 'Emergency Medicine']);
        $user = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.local',
            'phone' => '233000000000',
            'password' => 'Password123!',
            'role_id' => $role->id,
            'team_id' => $team->id,
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'login',
            'user_id' => $user->id,
        ]);
    }

    public function test_failed_login_is_audited(): void
    {
        Role::query()->create(['name' => 'Admin']);

        $response = $this->from('/login')->post('/login', [
            'email' => 'missing@test.local',
            'password' => 'bad-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'failed_login_attempt',
        ]);
    }

    public function test_logout_creates_audit_entry(): void
    {
        $role = Role::query()->create(['name' => 'Admin']);
        $team = Team::query()->create(['name' => 'Emergency Medicine']);
        $user = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.local',
            'phone' => '233000000000',
            'password' => 'Password123!',
            'role_id' => $role->id,
            'team_id' => $team->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'logout',
            'user_id' => $user->id,
        ]);
    }
}
