<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RekapKerjaSamaTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user superadmin (dekanat) dan login
        $this->user = User::factory()->create([
            'role' => 'dekanat',
        ]);

        $this->actingAs($this->user);
    }

    public function test_sukses_input_dengan_data_lengkap()
    {
        Storage::fake('public');

        $response = $this->postJson(route('rekapkerjasama.store'), [
            'noDokumen' => 'MOU-001',
            'unit' => 'Informatika',
            'mitraKerjaSama' => 'PT ABC Indonesia',
            'judulKerjaSama' => 'Pengembangan Kurikulum',
            'bentukKerjaSama' => ['Pendidikan'],
            'jenisKerjaSama' => 'MoU',
            'pihakUKDW' => 'Dr. John',
            'pihakMitra' => 'Bapak Budi',
            'tanggalMulai' => '2025-01-01',
            'tanggalSelesai' => '2025-12-31',
            'kategori' => 'nasional',
            'inKind' => '5000000',
            'totalInKind' => '5000000',
            'inCash' => '2000000',
            'totalInCash' => '2000000',
            'jumlahImplementasi' => '3',
            'dokumenPendukung' => UploadedFile::fake()->create('dokumen.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(200); // karena controller return response()->json(...)
        $response->assertJson([
            'success' => true,
            'message' => 'Data kerja sama berhasil disimpan!',
        ]);

        $this->assertDatabaseHas('rekapkerjasama', [
            'no_dokumen' => 'MOU-001',
            'unit' => 'Informatika',
        ]);
    }

    public function test_gagal_input_ketika_field_dikosongkan()
    {
        $response = $this->postJson(route('rekapkerjasama.store'), [
            'unit' => 'Informatika', // hanya isi satu field
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'noDokumen',
            'mitraKerjaSama',
            'judulKerjaSama',
            'bentukKerjaSama',
            'jenisKerjaSama',
            'pihakUKDW',
            'pihakMitra',
            'tanggalMulai',
            'tanggalSelesai',
            'kategori',
            'dokumenPendukung',
        ]);
    }

    public function test_gagal_input_dengan_karakter_non_angka_pada_inkind_dan_incash()
    {
        Storage::fake('public');

        $response = $this->postJson(route('rekapkerjasama.store'), [
            'noDokumen' => 'MOU-002',
            'unit' => 'Sistem Informasi',
            'mitraKerjaSama' => 'PT XYZ',
            'judulKerjaSama' => 'Penelitian Bersama',
            'bentukKerjaSama' => ['Penelitian'],
            'jenisKerjaSama' => 'MoU',
            'pihakUKDW' => 'Dr. Jane',
            'pihakMitra' => 'Ibu Sari',
            'tanggalMulai' => '2025-01-01',
            'tanggalSelesai' => '2025-12-31',
            'kategori' => 'nasional',
            'inKind' => 'lima juta',
            'totalInKind' => 'lima juta',
            'inCash' => 'dua juta',
            'totalInCash' => 'dua juta',
            'jumlahImplementasi' => '2',
            'dokumenPendukung' => UploadedFile::fake()->create('file.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'inKind',
            'totalInKind',
            'inCash',
            'totalInCash',
        ]);
    }

    public function test_gagal_input_jumlah_implementasi_dengan_huruf()
    {
        Storage::fake('public');

        $response = $this->postJson(route('rekapkerjasama.store'), [
            'noDokumen' => 'MOU-003',
            'unit' => 'FTI',
            'mitraKerjaSama' => 'Universitas ABC',
            'judulKerjaSama' => 'Pengabdian Masyarakat',
            'bentukKerjaSama' => ['Pengabdian'],
            'jenisKerjaSama' => 'MoU',
            'pihakUKDW' => 'Dekan FTI',
            'pihakMitra' => 'Ketua LPM ABC',
            'tanggalMulai' => '2025-01-01',
            'tanggalSelesai' => '2025-12-31',
            'kategori' => 'internasional',
            'inKind' => '1000000',
            'totalInKind' => '1000000',
            'inCash' => '500000',
            'totalInCash' => '500000',
            'jumlahImplementasi' => 'tiga', // error disini
            'dokumenPendukung' => UploadedFile::fake()->create('doc.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['jumlahImplementasi']);
    }
}
