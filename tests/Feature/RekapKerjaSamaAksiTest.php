<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RekapKerjaSama;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class RekapKerjaSamaAksiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function lihat_pdf_berhasil()
    {
        Storage::fake('public'); // Aktifkan fake storage

        $rekap = RekapKerjaSama::factory()->withPdf('dokumen_testing.pdf')->create();

        // Gunakan assertion khusus fake storage
        Storage::disk('public')->assertExists($rekap->dokumen_path);

        $response = $this->get(route('rekapkerjasama.pdf', $rekap->id));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function tombol_edit_menampilkan_halaman_dan_data_rekap()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $rekap = RekapKerjaSama::factory()->create([
            'judul_kerja_sama' => 'Judul Kerja Sama Testing',
        ]);

        $response = $this->get(route('rekapkerjasama.edit', $rekap->id));
        $response->assertStatus(200);
        $response->assertSee('Judul Kerja Sama Testing');
    }

    /** @test */
    public function tombol_hapus_menghapus_dokumen_kerja_sama()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $filePath = 'dokumen_kerja_sama/contoh.pdf';
        Storage::disk('public')->put($filePath, 'Dummy File');

        $rekap = RekapKerjaSama::factory()->create([
            'dokumen_path' => $filePath
        ]);

        $this->assertDatabaseHas('rekapkerjasama', ['id' => $rekap->id]);
        $this->assertTrue(Storage::disk('public')->exists($filePath));

        $response = $this->delete(route('rekapkerjasama.delete', $rekap->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus!'
        ]);

        $this->assertDatabaseMissing('rekapkerjasama', ['id' => $rekap->id]);
        $this->assertFalse(Storage::disk('public')->exists($filePath));
    }
}
