<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RekapKerjaSama;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelaksanaanKerjaSamaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_berhasil_input_dengan_data_lengkap()
    {
        $rekap = RekapKerjaSama::factory()->create();

        $response = $this->post('/pelaksanaankerjasama', [
            'rekap_id' => $rekap->id,
            'ruang_lingkup' => 'Pengabdian masyarakat',
            'dosen_terlibat' => 'Dr. Budi',
            'mahasiswa_terlibat' => 'Andi, Budi',
            'anggaran_ukdw' => 1000000,
            'hasil_pelaksanaan' => 'Berjalan lancar',
            'tautan_link_kegiatan' => 'https://example.com/kegiatan',
        ]);

        $response->assertRedirect(); 
        $this->assertDatabaseHas('pelaksanaankerjasama', [
            'idrekap' => $rekap->id,
            'ruang_lingkup' => 'Pengabdian masyarakat',
        ]);
    }

    public function test_gagal_input_kosongkan_form()
    {
        $response = $this->postJson(route('pelaksanaankerjasama.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'rekap_id',
            'ruang_lingkup',
            'dosen_terlibat',
            'mahasiswa_terlibat',
            'anggaran_ukdw',
            'hasil_pelaksanaan',
        ]);
    }

    public function test_input_anggaran_dengan_huruf()
    {
        $rekap = RekapKerjaSama::factory()->create();

        $response = $this->postJson(route('pelaksanaankerjasama.store'), [
            'rekap_id' => $rekap->id,
            'ruang_lingkup' => 'Pengabdian masyarakat',
            'dosen_terlibat' => 'Dosen A',
            'mahasiswa_terlibat' => 'Mahasiswa B',
            'anggaran_ukdw' => 'satu juta',
            'hasil_pelaksanaan' => 'Kegiatan berjalan baik',
            'tautan_link_kegiatan' => 'https://youtube.com/kegiatan',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['anggaran_ukdw']);
    }

    public function test_input_tautan_tidak_valid()
    {
        $rekap = RekapKerjaSama::factory()->create();

        $response = $this->postJson(route('pelaksanaankerjasama.store'), [
            'rekap_id' => $rekap->id,
            'ruang_lingkup' => 'Penelitian',
            'dosen_terlibat' => 'Dosen A',
            'mahasiswa_terlibat' => 'Mahasiswa B',
            'anggaran_ukdw' => 2000000,
            'hasil_pelaksanaan' => 'Kegiatan berhasil',
            'tautan_link_kegiatan' => 'ini bukan link',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tautan_link_kegiatan']);
    }
}
