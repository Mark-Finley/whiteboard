<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEmailValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_user_with_non_hospital_email(): void
    {
        [$admin] = $this->makeAdminContext();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'External User',
            'email' => 'external@example.com',
            'phone' => '233000000009',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role_id' => Role::query()->where('name', 'Ward Staff')->value('id'),
            'team_id' => null,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', [
            'email' => 'external@example.com',
        ]);
    }

    public function test_admin_can_create_user_with_kath_email(): void
    {
        [$admin] = $this->makeAdminContext();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Hospital User',
            'email' => 'hospital@kath.gov.gh',
            'phone' => '233000000010',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role_id' => Role::query()->where('name', 'Ward Staff')->value('id'),
            'team_id' => null,
            'status' => 'active',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'email' => 'hospital@kath.gov.gh',
        ]);
    }

    private function makeAdminContext(): array
    {
        $adminRole = Role::query()->create(['name' => 'Admin']);
        $wardRole = Role::query()->create(['name' => 'Ward Staff']);
        $team = Team::query()->create(['name' => 'Emergency Medicine']);

        $admin = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@kath.gov.gh',
            'phone' => '233000000000',
            'password' => 'Password123!',
            'role_id' => $adminRole->id,
            'team_id' => $team->id,
            'status' => 'active',
        ]);

        return [$admin, $wardRole, $team];
    }
}
