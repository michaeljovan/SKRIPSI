<?php

namespace Tests\Feature;

use App\Models\RekapKerjaSama;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\AssertableFilesystem;


class RekapKerjaSamaTest extends TestCase
{
    use RefreshDatabase;

    public function test_menyimpan_dokumen_kerja_sama()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $dokumen = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $response = $this->postJson('/rekapkerjasama', [
            'noDokumen' => 'DOC001',
            'unit' => 'Fakultas Teknik',
            'mitraKerjaSama' => 'PT ABC',
            'judulKerjaSama' => 'Penelitian Bersama',
            'bentukKerjaSama' => ['Penelitian'],
            'jenisKerjaSama' => 'MoU',
            'pihakUKDW' => 'UKDW',
            'pihakMitra' => 'PT ABC',
            'tanggalMulai' => '2024-01-01',
            'tanggalSelesai' => '2024-12-31',
            'kategori' => 'nasional',
            'in_kind' => 0,
            'total_in_kind' => 0,
            'in_cash' => 0,
            'total_in_cash' => 0,
            'jumlahImplementasi' => 2,
            'dokumenPendukung' => $dokumen,
            'parent_id' => 'none',
        ]);




        $response->assertStatus(200);
        $this->assertDatabaseHas('rekapkerjasama', ['no_dokumen' => 'DOC001']);

        Storage::disk('public')->assertExists('dokumen_kerja_sama/' . $dokumen->hashName());
    }

    public function test_validasi_store_gagal_jika_tidak_isi_field()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->postJson('/rekapkerjasama', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['noDokumen', 'unit', 'mitraKerjaSama']);
    }

    public function test_lihat_pdf_berhasil()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('file.pdf', 100);
        $path = $file->store('dokumen_kerja_sama', 'public');

        $rekap = RekapKerjaSama::factory()->create(['dokumen_path' => $path]);

        $response = $this->get('/rekapkerjasama/' . $rekap->id . '/pdf');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_lihat_pdf_gagal_jika_tidak_ada_file()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $rekap = RekapKerjaSama::factory()->create(['dokumen_path' => 'tidak_ada.pdf']);

        $response = $this->get('/rekapkerjasama/' . $rekap->id . '/pdf');
        $response->assertStatus(404);
    }

    public function test_update_berhasil()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');
        $oldFile = UploadedFile::fake()->create('old.pdf', 50);
        $oldPath = $oldFile->store('dokumen_kerja_sama', 'public');

        $rekap = RekapKerjaSama::factory()->create([
            'no_dokumen' => 'DOC123',
            'dokumen_path' => $oldPath
        ]);

        $newFile = UploadedFile::fake()->create('new.pdf', 100);

        $response = $this->putJson('/rekapkerjasama/' . $rekap->id, [
            'noDokumen' => 'DOC123',
            'unit' => 'Fakultas Sains',
            'mitraKerjaSama' => 'PT XYZ',
            'judulKerjaSama' => 'Penelitian Baru',
            'bentukKerjaSama' => ['Pendidikan'],
            'jenisKerjaSama' => 'MoU',
            'pihakUKDW' => 'UKDW',
            'pihakMitra' => 'PT XYZ',
            'tanggalMulai' => '2024-01-01',
            'tanggalSelesai' => '2024-12-31',
            'kategori' => 'nasional',
            'in_kind' => 0,
            'total_in_kind' => 0,
            'in_cash' => 0,
            'total_in_cash' => 0,
            'jumlahImplementasi' => 3,
            'dokumenPendukung' => $newFile,
        ]);



        $response->assertStatus(200);
        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'judul_kerja_sama' => 'Penelitian Baru'
        ]);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists('dokumen_kerja_sama/' . $newFile->hashName());
    }

    public function test_hapus_data_berhasil()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');

        $file = UploadedFile::fake()->create('hapus.pdf', 50);
        $path = $file->store('dokumen_kerja_sama', 'public');

        $rekap = RekapKerjaSama::factory()->create(['dokumen_path' => $path]);

        $response = $this->delete('/rekapkerjasama/' . $rekap->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('rekapkerjasama', ['id' => $rekap->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_get_dokumen_induk_moa_mengambil_mou()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        RekapKerjaSama::factory()->create(['jenis_kerja_sama' => 'MoU']);

        $response = $this->getJson('/api/dokumen-induk?jenis=MoA');
        $response->assertStatus(200);
        $response->assertJsonFragment(['no_dokumen' => 'Tidak Ada Induk']);
    }

    public function test_cek_no_dokumen()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        RekapKerjaSama::factory()->create(['no_dokumen' => 'ABC123']);

        $response = $this->getJson('/cek-nodokumen?no_dokumen=ABC123');

        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);
    }

    public function test_cek_no_dokumen_tidak_ada()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/cek-nodokumen?no_dokumen=XYZ999');

        $response->assertStatus(200);
        $response->assertJson(['exists' => false]);
    }
}
