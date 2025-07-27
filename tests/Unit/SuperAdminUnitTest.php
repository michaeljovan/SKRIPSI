<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dapat_menyimpan_user_baru_dengan_data_valid()
    {
        $data = [
            'name' => 'Unit Tester',
            'email' => 'unit@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'staff',
        ];

        $response = $this->withoutMiddleware()
                         ->post('/superadmin/store_user', $data);

        $response->assertSessionHas('success', 'User berhasil dibuat!');

        $this->assertDatabaseHas('users', [
            'email' => 'unit@example.com',
            'role' => 'staff',
        ]);
    }

    /** @test */
    public function dapat_mengubah_password_user_dengan_input_valid()
    {
        $user = User::factory()->create([
            'password' => bcrypt('passwordlama'),
        ]);

        $data = [
            'user_id' => $user->id,
            'new_password' => 'passwordbaru123',
            'new_password_confirmation' => 'passwordbaru123',
        ];

        $response = $this->withoutMiddleware()
                         ->post('/superadmin/change_password', $data);

        $response->assertJson([
            'success' => true,
            'message' => 'Password berhasil diubah',
        ]);
    }

    /** @test */
    public function dapat_menghapus_user_dari_database()
    {
        $user = User::factory()->create();

        $response = $this->withoutMiddleware()
                         ->delete('/superadmin/users/' . $user->id);

        $response->assertJson([
            'success' => true,
            'message' => 'User berhasil dihapus',
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
