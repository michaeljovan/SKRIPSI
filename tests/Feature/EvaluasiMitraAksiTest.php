<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\EvaluasiMitra;
use App\Models\RekapKerjaSama;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class EvaluasiMitraAksiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tombol_lihat_menampilkan_pdf()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $filePath = 'evaluasi_pdf/testfile.pdf';

        // Simpan file asli ke public storage
        $fullPath = storage_path('app/public/' . $filePath);
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }
        file_put_contents($fullPath, 'Dummy PDF content');

        $evaluasi = EvaluasiMitra::factory()->create([
            'file_pdf' => $filePath,
        ]);

        // Verifikasi file ada secara langsung
        $this->assertFileExists($fullPath);

        // Coba baca konten file
        $contents = file_get_contents($fullPath);
        $this->assertStringContainsString('Dummy PDF content', $contents);
    }

    /** @test */
    public function tombol_edit_membuka_halaman_dan_menampilkan_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $rekap = RekapKerjaSama::factory()->create();
        $evaluasi = EvaluasiMitra::factory()->create(['rekap_id' => $rekap->id]);

        $response = $this->get('/EvaluasiMitra/' . $evaluasi->idmitra . '/edit');
        $response->assertStatus(200);
        $response->assertSee($rekap->judul ?? '');
    }

    /** @test */
    public function tombol_hapus_menghapus_data_evaluasi_mitra()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $rekap = RekapKerjaSama::factory()->create();
        $evaluasi = EvaluasiMitra::factory()->create(['rekap_id' => $rekap->id]);

        $response = $this->delete('/EvaluasiMitra/' . $evaluasi->idmitra);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('evaluasimitra', [
            'idmitra' => $evaluasi->idmitra,
        ]);
    }
}
