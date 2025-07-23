<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitraKinerja;

class EvaluasiKerjaSamaMitraKinerjaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dapat_menyimpan_evaluasi_dengan_input_yang_benar()
    {
        $this->withoutMiddleware(); // hilangkan middleware seperti auth, csrf, dll

        Storage::fake('public'); // agar file tidak benar-benar disimpan
        $rekap = RekapKerjaSama::factory()->create();
        $file = UploadedFile::fake()->create('evaluasi.pdf', 500, 'application/pdf');

        $response = $this->post('/EvaluasiMitraKinerja', [
            'rekap_id' => $rekap->id,
            'nodok' => 'ND-2025-001',
            'mitra' => 'PT Mitra Hebat',
            'integritas' => 'Tinggi',
            'keahlian' => 'Tinggi',
            'komunikasi' => 'Tinggi',
            'kerjasamatim' => 'Tinggi',
            'pengembangandiri' => 'Tinggi',
            'kreativitas' => 'Tinggi',
            'bahasaasing' => 'Tinggi',
            'teknologi' => 'Tinggi',
            'manajerial' => 'Tinggi',
            'analisis' => 'Tinggi',
            'laporan' => 'Tinggi',
            'inovasi' => 'Tinggi',
            'lainlainlabel' => 'Komitmen',
            'lainlainnilai' => 'Tinggi',
            'komentar' => 'Sangat baik',
            'pdfFile' => $file,
        ]);

        $response->assertRedirect(); // Redirect sukses

        // Pastikan data masuk ke DB
        $this->assertDatabaseHas('evaluasimitrakinerja', [
            'nodok' => 'ND-2025-001',
            'mitra' => 'PT Mitra Hebat',
            'komentar' => 'Sangat baik',
        ]);

        // File tersimpan di storage yang difake
        Storage::disk('public')->assertExists('evaluasi_pdf/' . $file->hashName());

        // Pastikan is_kinerja = true setelah evaluasi disimpan
        $this->assertTrue((bool) $rekap->fresh()->is_kinerja);
    }

    /** @test */
    public function menampilkan_error_jika_input_kosong()
    {
        $this->withoutMiddleware();

        $response = $this->followingRedirects()->post('/EvaluasiMitraKinerja', []);

        $response->assertSessionHasErrors([
            'rekap_id',
            'nodok',
            'mitra',
            'integritas',
            'keahlian',
            'komunikasi',
            'kerjasamatim',
            'pengembangandiri',
            'kreativitas',
            'bahasaasing',
            'teknologi',
            'manajerial',
            'analisis',
            'laporan',
            'inovasi',
        ]);
    }

    /** @test */
    public function tombol_reset_menghapus_semua_input_form()
    {
        $this->withoutMiddleware();

        $response = $this->followingRedirects()->post('/EvaluasiMitraKinerja', [
            // Simulasi tekan tombol reset → tidak mengirim field apa pun
        ]);

        $response->assertSessionHasErrors(); // Harus gagal validasi

        // Tidak boleh ada data masuk
        $this->assertDatabaseCount('evaluasimitrakinerja', 0);
    }
}
