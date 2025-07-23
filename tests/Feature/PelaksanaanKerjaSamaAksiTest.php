<?php

namespace Tests\Feature;

use App\Models\PelaksanaanKerjaSama;
use App\Models\RekapKerjaSama;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PelaksanaanKerjaSamaAksiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dapat_melihat_pdf_dari_tombol_mata()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');
        $filePath = $file->store('dokumen_kegiatan', 'public');

        $rekap = RekapKerjaSama::factory()->create();
        $pelaksanaan = PelaksanaanKerjaSama::factory()->create([
            'idrekap' => $rekap->id,
            'dokumen_kegiatan' => $filePath,
        ]);

        Storage::disk('public')->assertExists($filePath);
    }

    /** @test */
    public function dapat_mengakses_halaman_edit_dan_melihat_data_lama()
    {
        $this->actingAs(User::factory()->create());

        $rekap = RekapKerjaSama::factory()->create();
        $pelaksanaan = PelaksanaanKerjaSama::factory()->create([
            'idrekap' => $rekap->id,
            'ruang_lingkup' => 'Tes Lingkup',
        ]);

        $response = $this->get(route('pelaksanaankerjasama.edit', $pelaksanaan->id));
        $response->assertStatus(200);
        $response->assertSee('Tes Lingkup');
    }

    /** @test */
    public function dapat_menghapus_pelaksanaan_dan_mengupdate_status_rekap()
    {
        $this->actingAs(User::factory()->create());

        $rekap = RekapKerjaSama::factory()->create([
            'is_laporan' => true,
        ]);
        $pelaksanaan = PelaksanaanKerjaSama::factory()->create([
            'idrekap' => $rekap->id,
        ]);

        $response = $this->delete(route('pelaksanaankerjasama.destroy', $pelaksanaan->id));
        $response->assertRedirect(route('pelaksanaankerjasama.index'));

        $this->assertDatabaseMissing('pelaksanaankerjasama', ['id' => $pelaksanaan->id]);
        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'is_laporan' => false,
        ]);
    }
}
