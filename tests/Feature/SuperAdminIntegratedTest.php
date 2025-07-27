<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SuperAdminIntegratedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function superadmin_dapat_melakukan_alur_tambah_ubahpassword_dan_hapus_user()
    {
        // Buat akun superadmin dan login
        $admin = User::factory()->create(['role' => 'dekanat']);
        $this->actingAs($admin);

        // Tambah user baru
        $response = $this->post('/superadmin/store_user', [
            'name' => 'Feature User',
            'email' => 'feature@example.com',
            'password' => 'pass1234',
            'password_confirmation' => 'pass1234',
            'role' => 'staff'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'feature@example.com']);

        $createdUser = User::where('email', 'feature@example.com')->first();

        // Ubah password user
        $changePassword = $this->postJson('/superadmin/change_password', [
            'user_id' => $createdUser->id,
            'new_password' => 'newpass123',
            'new_password_confirmation' => 'newpass123',
        ]);

        $changePassword->assertJson(['success' => true]);
        $this->assertTrue(Hash::check('newpass123', $createdUser->fresh()->password));

        // Hapus user
        $delete = $this->deleteJson("/superadmin/users/{$createdUser->id}");
        $delete->assertJson(['success' => true]);

        $this->assertDatabaseMissing('users', ['id' => $createdUser->id]);
    }
}
