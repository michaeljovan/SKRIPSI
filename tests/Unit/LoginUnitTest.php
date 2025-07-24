<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginUnitTest extends TestCase
{
    public function test_user_email_checking_logic()
    {
        $user = User::factory()->create(['email' => 'admin@example.com']);

        $fetchedUser = User::where('email', 'admin@example.com')->first();

        $this->assertNotNull($fetchedUser);
        $this->assertEquals($user->email, $fetchedUser->email);
    }

    public function test_password_verification_logic()
    {
        $password = 'secret123';
        $user = User::factory()->create([
            'password' => bcrypt($password)
        ]);

        $this->assertTrue(Hash::check('secret123', $user->password));
        $this->assertFalse(Hash::check('wrongpass', $user->password));
    }
}
