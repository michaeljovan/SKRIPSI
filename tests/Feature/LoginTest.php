<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'xxx@gmail.com',
            'password' => bcrypt('xxx123'),
        ]);

        $response = $this->post('/login', [
            'user' => 'xxx@gmail.com',
            'password' => 'xxx123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_without_email_and_password()
    {
        $response = $this->post('/login', [
            'user' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['user', 'password']);
        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_login_with_unregistered_email()
    {
        $response = $this->post('/login', [
            'user' => 'unregistered@gmail.com',
            'password' => 'somepassword',
        ]);

        $response->assertSessionHasErrors(['user']);
        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_login_with_wrong_password()
    {
        User::factory()->create([
            'email' => 'xxx@gmail.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->post('/login', [
            'user' => 'xxx@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }
}
