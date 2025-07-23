<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pengguna_dapat_login_dengan_kredensial_valid()
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
    public function pengguna_tidak_dapat_login_tanpa_email_dan_password()
    {
        $response = $this->post('/login', [
            'user' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['user', 'password']);
        $this->assertGuest();
    }

    /** @test */
    public function pengguna_tidak_dapat_login_dengan_email_yang_tidak_terdaftar()
    {
        $response = $this->post('/login', [
            'user' => 'unregistered@gmail.com',
            'password' => 'somepassword',
        ]);

        $response->assertSessionHasErrors(['user']);
        $this->assertGuest();
    }

    /** @test */
    public function pengguna_tidak_dapat_login_dengan_password_salah()
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
