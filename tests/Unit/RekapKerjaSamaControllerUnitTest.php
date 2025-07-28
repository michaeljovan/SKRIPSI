<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\RekapKerjaSama;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\RekapKerjaSamaController;
use App\Services\RekapKerjaSamaService;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RekapKerjaSamaControllerUnitTest extends TestCase
{
    protected $controller;
    protected $serviceMock;
    protected $user;

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(RekapKerjaSamaService::class);
        $this->controller = new RekapKerjaSamaController($this->serviceMock);

        // Create and authenticate a test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function menampilkan_data_dokumen_kerja_sama()
    {
        $mockData = RekapKerjaSama::factory()->count(5)->create();

        $response = $this->get(route('data_kerja_sama'));

        $response->assertStatus(200);
        $response->assertViewIs('datadokumenkerjasama');
        $response->assertViewHas('rekapKerjaSama');
    }


    /** @test */
    public function mengecek_apakah_dokumen_sudah_ada()
    {
        // Create a document that should exist
        RekapKerjaSama::factory()->create(['no_dokumen' => '12345']);

        $response = $this->get(route('cek.no_dokumen', ['no_dokumen' => '12345']));

        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);
    }


    /** @test */
    public function melakukan_input_kerja_sama_baru()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 1024);


        $uniqueNoDokumen = 'DOC-' . uniqid();

        $data = [
            'noDokumen' => $uniqueNoDokumen,
            'unit' => 'FIK',
            'mitraKerjaSama' => 'PT ABC',
            'judulKerjaSama' => 'Kerja Sama Pendidikan',
            'bentukKerjaSama' => ['Pendidikan'],
            'jenisKerjaSama' => 'MoU',
            'pihakUKDW' => 'Rektor',
            'pihakMitra' => 'Direktur',
            'tanggalMulai' => '2023-01-01',
            'tanggalSelesai' => '2023-12-31',
            'kategori' => 'nasional',
            'inKind' => '1000000',
            'totalInKind' => '1000000',
            'inCash' => '500000',
            'totalInCash' => '500000',
            'jumlahImplementasi' => 1,
            'dokumenPendukung' => $file,
            'parent_id' => 'none',
        ];

        $response = $this->post(route('rekapkerjasama.store'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Data kerja sama berhasil disimpan!'
        ]);

        $this->assertDatabaseHas('rekapkerjasama', [
            'no_dokumen' => $uniqueNoDokumen,
            'judul_kerja_sama' => 'Kerja Sama Pendidikan'
        ]);
    }

    /** @test */
    public function validasi_untuk_kerja_sama_baru()
    {
        $response = $this->post(route('rekapkerjasama.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'noDokumen',
            'unit',
            'mitraKerjaSama',
            'judulKerjaSama',
            'bentukKerjaSama',
            'jenisKerjaSama',
            'pihakUKDW',
            'pihakMitra',
            'tanggalMulai',
            'tanggalSelesai',
            'kategori',
            'dokumenPendukung'
        ]);
    }

    /** @test */
    public function validasi_menghapus_kerja_sama()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('document.pdf', 1024);
        $path = $file->store('dokumen_kerja_sama', 'public');

        $kerjaSama = RekapKerjaSama::factory()->create([
            'dokumen_path' => $path
        ]);

        $response = $this->delete(route('rekapkerjasama.delete', $kerjaSama->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus!'
        ]);

        $this->assertDatabaseMissing('rekapkerjasama', ['id' => $kerjaSama->id]);
        Storage::disk('public')->assertMissing($path);
    }

    /** @test */
    public function validasi_dokumen_induk_dapat_tertampil()
    {
        $mou = RekapKerjaSama::factory()->create(['jenis_kerja_sama' => 'MoU']);
        $moa = RekapKerjaSama::factory()->create(['jenis_kerja_sama' => 'MoA']);

        // Test for MoA
        $response = $this->get(route('api.dokumen_induk', ['jenis' => 'MoA']));
        $response->assertStatus(200);
        $response->assertJsonFragment(['no_dokumen' => $mou->no_dokumen]);

        // Test for IA
        $response = $this->get(route('api.dokumen_induk', ['jenis' => 'IA']));
        $response->assertStatus(200);
        $response->assertJsonFragment(['no_dokumen' => $mou->no_dokumen]);
        $response->assertJsonFragment(['no_dokumen' => $moa->no_dokumen]);

        // Test for invalid jenis
        $response = $this->get(route('api.dokumen_induk', ['jenis' => 'INVALID']));
        $response->assertStatus(400);
    }

    /** @test */
    public function validasi_edit_kerja_sama_dapat_terambil()
    {
        $kerjaSama = RekapKerjaSama::factory()->create(['jenis_kerja_sama' => 'MoA']);

        $response = $this->get(route('rekapkerjasama.edit', $kerjaSama->id));

        $response->assertStatus(200);
        $response->assertViewIs('editrekapkerjasama');
        $response->assertViewHas('rekap', $kerjaSama);
    }

    /** @test */
    public function validasi_untuk_mengirim_edit_kerja_sama()
    {
        Storage::fake('public');

        $parent = RekapKerjaSama::factory()->create(['jenis_kerja_sama' => 'MoU']);

        $kerjaSama = RekapKerjaSama::factory()->create();

        $data = [
            'noDokumen' => 'DOC-UPDATED',
            'unit' => 'FIK Updated',
            'mitraKerjaSama' => 'PT ABC Updated',
            'judulKerjaSama' => 'Kerja Sama Updated',
            'bentukKerjaSama' => ['Penelitian'],
            'jenisKerjaSama' => 'MoA',
            'pihakUKDW' => 'Rektor Updated',
            'pihakMitra' => 'Direktur Updated',
            'tanggalMulai' => '2023-01-01',
            'tanggalSelesai' => '2023-12-31',
            'kategori' => 'internasional',
            'in_kind' => '2000000',
            'totalInKind' => '2000000',
            'inCash' => '1000000',
            'totalInCash' => '1000000',
            'jumlahImplementasi' => 2,
            'parent_id' => $parent->id,
            '_token' => csrf_token(),
        ];

        $response = $this->put(route('rekapkerjasama.update', $kerjaSama->id), $data);

        $response->assertStatus(302);
        $response->assertRedirect(route('data_kerja_sama'));
        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $kerjaSama->id,
            'no_dokumen' => 'DOC-UPDATED'
        ]);
    }

    /** @test */
    public function valdasi_menampilkan_pdf()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');
        $path = $file->store('dokumen_kerja_sama', 'public');

        $kerjaSama = RekapKerjaSama::factory()->create([
            'dokumen_path' => $path
        ]);

        $response = $this->get(route('rekapkerjasama.pdf', $kerjaSama->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function validasi_jika_pdf_kosong()
    {
        $kerjaSama = RekapKerjaSama::factory()->create([
            'dokumen_path' => 'non-existent-path.pdf'
        ]);

        $response = $this->get(route('rekapkerjasama.pdf', $kerjaSama->id));
        $response->assertStatus(404);
    }
}
