<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitraKinerja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class EvaluasiMitraKinerjaIntegratedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function menyimpan_evaluasi_dan_memperbarui_rekap()
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create());

        $rekap = RekapKerjaSama::factory()->create([
            'is_kinerja' => false
        ]);

        $data = [
            'rekap_id' => $rekap->id,
            'nodok' => 'ND-001',
            'mitra' => 'PT Mitra Hebat',
            'integritas' => 'Tinggi',
            'keahlian' => 'Cukup',
            'komunikasi' => 'Kurang',
            'kerjasamatim' => 'Tinggi',
            'pengembangandiri' => 'Tinggi',
            'kreativitas' => 'Tinggi',
            'bahasaasing' => 'Kurang',
            'teknologi' => 'Cukup',
            'manajerial' => 'Tinggi',
            'analisis' => 'Sangat Kurang',
            'laporan' => 'Cukup',
            'inovasi' => 'Cukup',
            'lainlainlabel' => 'Etika',
            'lainlainnilai' => 'Sangat Tinggi',
            'komentar' => 'Komentar uji',
            'pdfFile' => UploadedFile::fake()->create('test.pdf', 100, 'application/pdf'),
        ];

        $response = $this->post(route('EvaluasiMitraKinerja.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('evaluasimitrakinerja', [
            'nodok' => 'ND-001',
            'mitra' => 'PT Mitra Hebat',
        ]);

        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'is_kinerja' => true,
        ]);

        Storage::disk('public')->assertExists('evaluasi_pdf/' . basename(EvaluasiMitraKinerja::first()->file_pdf));
    }

    /** @test */
    public function menghapus_evaluasi_dan_memperbarui_rekap()
    {
        $this->actingAs(User::factory()->create());
        $rekap = RekapKerjaSama::factory()->create(['is_kinerja' => true]);

        $evaluasi = EvaluasiMitraKinerja::factory()->create([
            'rekap_id' => $rekap->id,
        ]);

        $response = $this->delete(route('EvaluasiMitraKinerja.delete', $evaluasi->idkinerja));

        $response->assertJson([
            'success' => true,
            'message' => 'Hasil evaluasi berhasil dihapus',
        ]);

        $this->assertDatabaseMissing('evaluasimitrakinerja', [
            'idkinerja' => $evaluasi->idkinerja,
        ]);

        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'is_kinerja' => false,
        ]);
    }
}
