<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Registrasi tidak boleh memberikan hak akses admin melalui input.
     */
    public function test_registration_cannot_assign_admin_role(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test Customer',
            'email' => 'customer-test@example.com',
            'phone' => '081234567890',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'admin',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'customer-test@example.com',
            'role' => 'customer',
            'is_active' => true,
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Akun nonaktif tidak boleh berhasil login.
     */
    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'customer',
            'is_active' => false,
        ])->save();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Akun nonaktif tidak boleh mengakses dashboard.
     */
    public function test_inactive_user_is_logged_out_when_accessing_dashboard(): void
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'customer',
            'is_active' => false,
        ])->save();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}