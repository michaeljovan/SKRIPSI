<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\PelaksanaanKerjaSama;
use App\Models\RekapKerjaSama;

class LaporanPelaksanaanKerjaSamaUnitTest extends TestCase
{
    use WithFaker, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_mengirim_laporan_pelaksanaan_baru_berhasil()
    {
        $rekap = RekapKerjaSama::factory()->create();

        $file = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $payload = [
            'rekap_id' => $rekap->id,
            'ruang_lingkup' => 'Penelitian Bersama',
            'dosen_terlibat' => 'Dr. A',
            'mahasiswa_terlibat' => 'John Doe',
            'anggaran_ukdw' => '100000',
            'hasil_pelaksanaan' => 'Kegiatan berjalan baik',
            'tautan_link_kegiatan' => 'https://example.com',
            'dokumen_kegiatan' => $file,
        ];

        $response = $this->post(route('pelaksanaankerjasama.store'), $payload);

        $response->assertRedirect(route('pelaksanaankerjasama.index'));
        $this->assertDatabaseHas('pelaksanaankerjasama', [
            'ruang_lingkup' => 'Penelitian Bersama',
            'idrekap' => $rekap->id,
        ]);

        Storage::disk('public')->assertExists('dokumen_kegiatan/' . $file->hashName());
    }

    public function test_mengirim_dengan_validasi_error()
    {
        $payload = [
            // rekam_id dihilangkan
            'ruang_lingkup' => '',
            'anggaran_ukdw' => 'abc', // invalid
        ];

        $response = $this->post(route('pelaksanaankerjasama.store'), $payload);
        $response->assertSessionHasErrors(['rekap_id', 'ruang_lingkup', 'anggaran_ukdw']);
    }

    public function test_update_edit_berhasil()
    {
        $pelaksanaan = PelaksanaanKerjaSama::factory()->create();
        $file = UploadedFile::fake()->create('update.pdf', 100, 'application/pdf');

        $payload = [
            'ruang_lingkup' => 'Update Kegiatan',
            'dosen_terlibat' => 'Updated Dosen',
            'mahasiswa_terlibat' => 'Updated Mahasiswa',
            'anggaran_ukdw' => '200000',
            'hasil_pelaksanaan' => 'Updated Hasil',
            'tautan_link_kegiatan' => 'https://updated.com',
            'dokumen_kegiatan' => $file,
        ];

        $response = $this->put(route('pelaksanaankerjasama.update', $pelaksanaan->id), $payload);

        $response->assertRedirect(route('pelaksanaankerjasama.index'));
        $this->assertDatabaseHas('pelaksanaankerjasama', [
            'id' => $pelaksanaan->id,
            'ruang_lingkup' => 'Update Kegiatan',
        ]);
    }

    public function test_hapus_laporan_pelaksanaan_berhasil()
    {
        $rekap = RekapKerjaSama::factory()->create(['is_laporan' => true]);
        $pelaksanaan = PelaksanaanKerjaSama::factory()->create([
            'idrekap' => $rekap->id
        ]);

        $response = $this->delete(route('pelaksanaankerjasama.destroy', $pelaksanaan->id));

        $response->assertRedirect(route('pelaksanaankerjasama.index'));
        $this->assertDatabaseMissing('pelaksanaankerjasama', ['id' => $pelaksanaan->id]);
        $this->assertDatabaseHas('rekapkerjasama', [
            'id' => $rekap->id,
            'is_laporan' => false
        ]);
    }
}
