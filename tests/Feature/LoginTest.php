<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_page_loads_successfully()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Login');
        $response->assertSee('User');
        $response->assertSee('Password');
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        // Create a user with email (since your controller uses email for auth)
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Note: The form uses 'user' field but controller expects it as email
        $response = $this->post(route('login.post'), [
            'user' => 'test@example.com', // This will be treated as email
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Login berhasil!');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_fails_with_invalid_email()
    {
        $response = $this->post(route('login.post'), [
            'user' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('loginError', 'Username atau password salah');
        $this->assertGuest();
    }

    /** @test */
    public function login_fails_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->post(route('login.post'), [
            'user' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('loginError', 'Username atau password salah');
        $this->assertGuest();
    }

    /** @test */
    public function login_requires_username()
    {
        $response = $this->post(route('login.post'), [
            'user' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('user');
    }

    /** @test */
    public function login_requires_password()
    {
        $response = $this->post(route('login.post'), [
            'user' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function login_page_contains_expected_elements()
    {
        $response = $this->get(route('login'));

        $response->assertSee('Fakultas Teknologi Informasi');
        $response->assertSee('fti-ukdw.png');
        $response->assertSee('logo-ukdw.png');
        $response->assertSee('type="password"', false);
        $response->assertSee('type="submit"', false);
    }
}
