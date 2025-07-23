<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SuperAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Simulasi login sebagai superadmin (dekanat)
        $this->superadmin = User::factory()->create([
            'role' => 'dekanat',
        ]);

        $this->actingAs($this->superadmin);
    }

    /** @test */
    public function dapat_menambahkan_user_dengan_data_valid()
    {
        $response = $this->post(route('superadmin.store_user'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'staff',
        ]);

        $response->assertRedirect(); // Redirect sukses
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /** @test */
    public function tidak_dapat_menambahkan_user_dengan_data_kosong()
    {
        $response = $this->post(route('superadmin.store_user'), [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'role' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
        $this->assertDatabaseMissing('users', ['email' => '']);
    }

    /** @test */
    public function dapat_menghapus_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson(route('superadmin.delete_user', $user->id));

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function tidak_dapat_mengubah_password_dengan_field_kosong()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('superadmin.change_password'), [
            'user_id' => $user->id,
            'new_password' => '',
            'new_password_confirmation' => '',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['new_password', 'new_password_confirmation']);
    }

    /** @test */
    public function dapat_mengubah_password_dengan_input_valid()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('superadmin.change_password'), [
            'user_id' => $user->id,
            'new_password' => 'tes123',
            'new_password_confirmation' => 'tes123',
        ]);

        $response->assertJson(['success' => true]);
        $this->assertTrue(Hash::check('tes123', $user->fresh()->password));
    }

    /** @test */
    public function tidak_dapat_mengubah_password_dengan_konfirmasi_tidak_sesuai()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('superadmin.change_password'), [
            'user_id' => $user->id,
            'new_password' => 'tes123',
            'new_password_confirmation' => 'tes321',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['new_password_confirmation']);
    }
}
