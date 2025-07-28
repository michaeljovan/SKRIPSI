<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\RekapKerjaSama;
use App\Models\PelaksanaanKerjaSama;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LaporanPelaksanaanKerjaSamaIntegratedTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function user_can_store_laporan_pelaksanaan()
    {
        $this->actingAs(User::factory()->create());

        $rekap = RekapKerjaSama::factory()->create();

        $file = UploadedFile::fake()->create('laporan.pdf', 200, 'application/pdf');

        $response = $this->post(route('pelaksanaankerjasama.store'), [
            'rekap_id' => $rekap->id,
            'ruang_lingkup' => 'Riset Gabungan',
            'dosen_terlibat' => 'Dr. Budi',
            'mahasiswa_terlibat' => 'Siti, Rudi',
            'anggaran_ukdw' => '500000',
            'hasil_pelaksanaan' => 'Berhasil dilaksanakan',
            'tautan_link_kegiatan' => 'https://example.com/kegiatan',
            'dokumen_kegiatan' => $file,
        ]);

        $response->assertRedirect(route('pelaksanaankerjasama.index'));
        $this->assertDatabaseHas('pelaksanaankerjasama', [
            'ruang_lingkup' => 'Riset Gabungan',
            'idrekap' => $rekap->id,
        ]);

        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'is_laporan' => true
        ]);

        Storage::disk('public')->assertExists('dokumen_kegiatan/' . $file->hashName());
    }

    /** @test */
    public function user_can_update_laporan_pelaksanaan()
    {
        $this->actingAs(User::factory()->create());

        $pelaksanaan = PelaksanaanKerjaSama::factory()->create([
            'dokumen_kegiatan' => null
        ]);

        $file = UploadedFile::fake()->create('update-laporan.pdf', 100, 'application/pdf');

        $response = $this->put(route('pelaksanaankerjasama.update', $pelaksanaan->id), [
            'ruang_lingkup' => 'Diklat Bersama',
            'dosen_terlibat' => 'Update Dosen',
            'mahasiswa_terlibat' => 'Update Mahasiswa',
            'anggaran_ukdw' => '200000',
            'hasil_pelaksanaan' => 'Update hasil',
            'tautan_link_kegiatan' => 'https://update.com',
            'dokumen_kegiatan' => $file,
        ]);

        $response->assertRedirect(route('pelaksanaankerjasama.index'));

        $this->assertDatabaseHas('pelaksanaankerjasama', [
            'id' => $pelaksanaan->id,
            'ruang_lingkup' => 'Diklat Bersama'
        ]);

        Storage::disk('public')->assertExists('dokumen_kegiatan/' . $file->hashName());
    }

    /** @test */
    public function user_can_delete_laporan_pelaksanaan()
    {
        $this->actingAs(User::factory()->create());

        $rekap = RekapKerjaSama::factory()->create(['is_laporan' => true]);
        $pelaksanaan = PelaksanaanKerjaSama::factory()->create([
            'idrekap' => $rekap->id
        ]);

        $response = $this->delete(route('pelaksanaankerjasama.destroy', $pelaksanaan->id));

        $response->assertRedirect(route('pelaksanaankerjasama.index'));

        $this->assertDatabaseMissing('pelaksanaankerjasama', [
            'id' => $pelaksanaan->id
        ]);

        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'is_laporan' => false
        ]);
    }
}
