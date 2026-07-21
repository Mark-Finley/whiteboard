<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_shows_signup_link(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Sign up', false)
            ->assertSee(route('register'), false);
    }

    public function test_guest_can_open_registration_page(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Create your hospital account', false);
    }

    public function test_registration_requires_kath_gov_gh_email(): void
    {
        Role::query()->create(['name' => 'Ward Staff']);
        $team = Team::query()->create(['name' => 'Emergency Medicine']);
        $ward = Ward::query()->create(['name' => 'TRIAGE HOLDING', 'color_code' => '#ccc']);

        $this->from('/register')->post('/register', [
            'name' => 'External User',
            'email' => 'external@example.com',
            'phone' => '233000000011',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'team_id' => $team->id,
            'ward_id' => $ward->id,
        ])->assertRedirect('/register')
          ->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('users', [
            'email' => 'external@example.com',
        ]);
    }

    public function test_registration_allows_kath_gov_gh_email(): void
    {
        Role::query()->create(['name' => 'Ward Staff']);
        $team = Team::query()->create(['name' => 'Emergency Medicine']);
        $ward = Ward::query()->create(['name' => 'TRIAGE HOLDING', 'color_code' => '#ccc']);

        $this->post('/register', [
            'name' => 'Hospital User',
            'email' => 'hospital@kath.gov.gh',
            'phone' => '233000000012',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'team_id' => $team->id,
            'ward_id' => $ward->id,
        ])->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'email' => 'hospital@kath.gov.gh',
        ]);
    }
}
