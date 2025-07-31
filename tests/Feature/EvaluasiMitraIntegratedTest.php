<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitra;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EvaluasiMitraIntegratedTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser()
    {
        $user = User::factory()->create();
        return $this->actingAs($user);
    }

    /** @test */
    public function user_dapat_akses()
    {
        $this->actingAsUser();

        $rekap = RekapKerjaSama::factory()->create();
        $evaluasi = EvaluasiMitra::factory()->create(['rekap_id' => $rekap->id]);

        $response = $this->get(route('EvaluasiMitra.edit', $evaluasi->idmitra));
        $response->assertStatus(200);
        $response->assertViewIs('evaluasikerjasamamitraedit');
        $response->assertViewHas(['evaluasi', 'rekap']);
    }

    /** @test */
    public function user_dapat_melakuakan_update_evaluasi_mitra()
    {
        Storage::fake('public');
        $this->actingAsUser();

        $rekap = RekapKerjaSama::factory()->create();
        $evaluasi = EvaluasiMitra::factory()->create(['rekap_id' => $rekap->id]);

        $file = UploadedFile::fake()->create('updated.pdf', 100, 'application/pdf');

        $response = $this->put(route('EvaluasiMitra.update', $evaluasi->idmitra), [
            'integritas' => 'Tinggi',
            'keahlian' => 'Cukup',
            'komunikasi' => 'Sangat Tinggi',
            'kerjasamatim' => 'Cukup',
            'pengembangandiri' => 'Tinggi',
            'kreativitas' => 'Kurang',
            'bahasaasing' => 'Cukup',
            'pdfFile' => $file,
        ]);

        $response->assertRedirect(route('EvaluasiMitra.index'));
        $this->assertDatabaseHas('evaluasimitra', [
            'idmitra' => $evaluasi->idmitra,
        ]);
    }

    /** @test */
    public function user_dapat_melakukan_hapus_evaluasi_mitra()
    {
        $this->actingAsUser();

        $rekap = RekapKerjaSama::factory()->create(['is_mitra' => true]);
        $evaluasi = EvaluasiMitra::factory()->create(['rekap_id' => $rekap->id]);

        $response = $this->delete(route('EvaluasiMitra.delete', $evaluasi->idmitra));

        $response->assertJson([
            'success' => true,
            'message' => 'Evaluasi mitra berhasil dihapus',
        ]);

        $this->assertDatabaseMissing('evaluasimitra', ['idmitra' => $evaluasi->idmitra]);
        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'is_mitra' => false,
        ]);
    }
}
