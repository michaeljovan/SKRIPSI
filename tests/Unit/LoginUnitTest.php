<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginUnitTest extends TestCase
{
    use RefreshDatabase;

    public function verifikasi_untuk_email_login()
    {
        $user = User::factory()->create(['email' => 'admin@example.com']);

        $fetchedUser = User::where('email', 'admin@example.com')->first();

        $this->assertNotNull($fetchedUser);
        $this->assertEquals($user->email, $fetchedUser->email);
    }

    public function verifikasi_untuk_password_login()
    {
        $password = 'secret123';
        $user = User::factory()->create([
            'password' => bcrypt($password)
        ]);

        $this->assertTrue(Hash::check('secret123', $user->password));
        $this->assertFalse(Hash::check('wrongpass', $user->password));
    }
}
