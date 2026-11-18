<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_admin_can_open_user_management(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('users.index'))->assertOk();
    }

    public function test_receptionist_cannot_open_user_management(): void
    {
        $receptionist = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);

        $this->actingAs($receptionist)->get(route('users.index'))->assertForbidden();
    }
}
