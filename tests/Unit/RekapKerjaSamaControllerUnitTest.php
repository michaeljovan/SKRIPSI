<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\RekapKerjaSama;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RekapKerjaSamaControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsDekanat()
    {
        $user = User::factory()->create(['role' => 'dekanat']);
        $this->actingAs($user);
    }

    /** @test */
    public function dapat_menampilkan_index_data()
    {
        $this->actingAsDekanat();
        $response = $this->get('/rekapkerjasama');
        $response->assertStatus(200);
    }

    /** @test */
    public function dapat_menampilkan_form_create()
    {
        $this->actingAsDekanat();
        $this->get('/rekapkerjasama/create')->assertStatus(200);
    }

    /** @test */
    /** @test */
    public function dapat_menyimpan_data_rekap_kerja_sama()
    {
        $this->actingAsDekanat();

        Storage::fake('public');

        $data = [
            'noDokumen' => 'DOC-001',
            'unit' => 'Fakultas Teknologi Informasi',
            'mitraKerjaSama' => 'PT. Mitra Hebat',
            'judulKerjaSama' => 'Kerja Sama Penelitian AI',
            'bentukKerjaSama' => ['Penelitian'], // Sesuai validasi in:Penelitian,Pendidikan,Pengabdian
            'jenisKerjaSama' => 'MoU', // Sesuai validasi in:MoU,MoA,IA
            'pihakUKDW' => 'UKDW',
            'pihakMitra' => 'PT. Mitra Hebat',
            'tanggalMulai' => '2024-01-01',
            'tanggalSelesai' => '2025-01-01',
            'kategori' => 'nasional', // Sesuai validasi in:nasional,internasional
            'inKind' => 10000,
            'totalInKind' => 20000,
            'inCash' => 15000,
            'totalInCash' => 25000,
            'jumlahImplementasi' => 3,
            'dokumenPendukung' => UploadedFile::fake()->create('file.pdf', 100),
        ];

        $response = $this->post('/rekapkerjasama', $data);

        $response->assertStatus(200); // karena controller return response()->json()
        $this->assertDatabaseHas('rekapkerjasama', [
            'no_dokumen' => 'DOC-001',
            'judul_kerja_sama' => 'Kerja Sama Penelitian AI',
        ]);
    }


    /** @test */
    public function dapat_mengambil_data_rekap_kerja_sama_untuk_diedit()
    {
        $this->actingAsDekanat();
        $rekap = RekapKerjaSama::factory()->create();
        $this->get("/rekapkerjasama/{$rekap->id}/edit")->assertStatus(200);
    }

    /** @test */
    public function dapat_melihat_pdf_dokumen()
    {
        $this->actingAsDekanat();
        Storage::fake('public');
        $rekap = RekapKerjaSama::factory()->withPdf()->create();

        $this->get("/rekapkerjasama/{$rekap->id}/pdf")->assertStatus(200);
    }

    /** @test */
    public function test_dapat_update_data_rekap_kerja_sama()
    {
        $this->actingAsDekanat();

        // Buat data awal
        $rekap = RekapKerjaSama::factory()->create([
            'judul_kerja_sama' => 'Lama',
            'dokumen_path' => 'dokumen_kerja_sama/file.pdf',
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('file.pdf', 100, 'application/pdf');

        $data = [
            'noDokumen' => $rekap->no_dokumen,
            'unit' => $rekap->unit,
            'mitraKerjaSama' => $rekap->mitra_kerja_sama,
            'judulKerjaSama' => 'Baru', // perubahan
            'bentukKerjaSama' => ['Penelitian'],
            'jenisKerjaSama' => 'MoU',
            'pihakUKDW' => $rekap->pihak_ukdw,
            'pihakMitra' => $rekap->pihak_mitra,
            'tanggalMulai' => $rekap->tanggal_mulai->format('Y-m-d'),
            'tanggalSelesai' => $rekap->tanggal_selesai->format('Y-m-d'),
            'kategori' => 'nasional',
            'dokumenPendukung' => $file,
        ];

        $this->put("/rekapkerjasama/{$rekap->id}", $data)->assertRedirect();

        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'judul_kerja_sama' => 'Baru',
        ]);
    }


    /** @test */
    public function dapat_menghapus_data_rekap_kerja_sama()
    {
        $this->actingAsDekanat();
        $rekap = RekapKerjaSama::factory()->create();

        $this->delete("/rekapkerjasama/{$rekap->id}")
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data berhasil dihapus!'
            ]);

        $this->assertDatabaseMissing('rekapkerjasama', [
            'id' => $rekap->id
        ]);
    }


    /** @test */
    public function dapat_mengambil_data_dokumen_induk()
    {
        $this->actingAsDekanat();

        RekapKerjaSama::factory()->create([
            'no_dokumen' => 'INDUK-123',
            'jenis_kerja_sama' => 'MoU',
        ]);

        $this->get('/api/dokumen-induk?jenis=IA')
            ->assertStatus(200)
            ->assertJsonFragment(['no_dokumen' => 'INDUK-123']);
    }



    /** @test */
    public function route_data_return_200()
    {
        $this->actingAsDekanat();
        $this->get('/rekapkerjasama')->assertStatus(200);
    }

    /** @test */
    /** @test */
    public function dapat_mengecek_ketersediaan_nomor_dokumen()
    {
        $this->actingAsDekanat();

        RekapKerjaSama::factory()->create([
            'no_dokumen' => 'DOC-777',
        ]);

        // Cek nomor yang sudah ada => exists: true
        $this->get('/cek-nodokumen?no_dokumen=DOC-777')
            ->assertStatus(200)
            ->assertJson(['exists' => true]);

        // Cek nomor yang belum ada => exists: false
        $this->get('/cek-nodokumen?no_dokumen=DOC-999')
            ->assertStatus(200)
            ->assertJson(['exists' => false]);
    }
}
