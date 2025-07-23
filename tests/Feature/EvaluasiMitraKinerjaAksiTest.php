<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitraKinerja;

class EvaluasiMitraKinerjaAksiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tombol_lihat_menampilkan_pdf()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $filePath = 'evaluasi_pdf/testfile.pdf';

        // Simpan file secara manual di folder public storage
        $fullPath = storage_path('app/public/' . $filePath);
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }
        file_put_contents($fullPath, 'Dummy PDF content');

        $evaluasi = EvaluasiMitraKinerja::factory()->create([
            'file_pdf' => $filePath,
        ]);

        $this->assertFileExists($fullPath);
        $this->assertStringContainsString('Dummy PDF content', file_get_contents($fullPath));
    }

    /** @test */
    public function tombol_edit_membuka_halaman_dan_menampilkan_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $rekap = RekapKerjaSama::factory()->create();
        $evaluasi = EvaluasiMitraKinerja::factory()->create([
            'rekap_id' => $rekap->id,
            'integritas' => 5,
        ]);

        $response = $this->get('/EvaluasiMitraKinerja/' . $evaluasi->idkinerja . '/edit');
        $response->assertStatus(200);
        $response->assertSee('5'); // nilai integritas ditampilkan di form
    }

    /** @test */
    public function tombol_hapus_menghapus_data_evaluasi_kinerja()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $rekap = RekapKerjaSama::factory()->create(['is_kinerja' => true]);
        $evaluasi = EvaluasiMitraKinerja::factory()->create(['rekap_id' => $rekap->id]);

        $response = $this->delete('/EvaluasiMitraKinerja/' . $evaluasi->idkinerja);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('evaluasimitrakinerja', [
            'idkinerja' => $evaluasi->idkinerja,
        ]);

        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'is_kinerja' => false
        ]);
    }
}
