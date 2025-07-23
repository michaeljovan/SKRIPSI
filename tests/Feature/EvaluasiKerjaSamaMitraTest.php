<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\EvaluasiMitra;
use App\Models\RekapKerjaSama;
use App\Models\User;

class EvaluasiKerjaSamaMitraTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dapat_menyimpan_evaluasi_dengan_input_yang_benar()
    {
        $this->withoutExceptionHandling();

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user); // ⬅️ Tambahkan ini!

        Storage::fake('public');

        $rekap = RekapKerjaSama::factory()->create();
        $file = UploadedFile::fake()->create('evaluasi.pdf', 100, 'application/pdf');

        $response = $this->post('/EvaluasiMitra', [
            'rekap_id' => $rekap->id,
            'nodok' => 'ND-2025-003',
            'mitra' => 'PT Mitra Andalan',
            'integritas' => 'Tinggi',
            'keahlian' => 'Tinggi',
            'komunikasi' => 'Cukup',
            'kerjasamatim' => 'Tinggi',
            'pengembangandiri' => 'Cukup',
            'kreativitas' => 'Tinggi',
            'bahasaasing' => 'Cukup',
            'teknologi' => 'Tinggi',
            'manajerial' => 'Cukup',
            'analisis' => 'Tinggi',
            'laporan' => 'Tinggi',
            'inovasi' => 'Tinggi',
            'lainlainlabel' => 'Kedisiplinan',
            'lainlainnilai' => 'Cukup',
            'komentar' => 'Mitra sangat kooperatif',
            'pdfFile' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('evaluasimitra', [
            'nodok' => 'ND-2025-003',
            'mitra' => 'PT Mitra Andalan',
        ]);

        Storage::disk('public')->assertExists('evaluasi_pdf/' . $file->hashName());
        $this->assertEquals(1, $rekap->fresh()->is_mitra);
    }



    /** @test */
    public function menampilkan_error_jika_input_kosong()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/EvaluasiMitra', []); // tanpa input

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
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);


        $rekap = RekapKerjaSama::factory()->create();

        $response = $this->post('/EvaluasiMitra', [
            // Simulasikan user klik reset sebelum isi dikirim (jadi kosong semua)
        ]);

        $response->assertSessionHasErrors(); // karena tidak ada data

        $this->assertDatabaseMissing('evaluasimitra', [
            'rekap_id' => $rekap->id,
        ]);
    }
}
