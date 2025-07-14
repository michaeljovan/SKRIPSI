<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PelaksanaanKerjaSama;
use App\Models\RekapKerjaSama;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelaksanaanKerjaSamaTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\RekapKerjaSama */
    protected $rekap;

    /** @var array */
    protected $payload;

    /** @var \App\Models\PelaksanaanKerjaSama|null */
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        $this->rekap = RekapKerjaSama::factory()->create();

        $this->payload = [
            'rekap_id' => $this->rekap->id,
            'ruang_lingkup' => 'Penelitian bersama bidang AI',
            'dosen_terlibat' => 'Dr. Rudi Santoso',
            'mahasiswa_terlibat' => 'Andi, Budi',
            'anggaran_ukdw' => 10000000,
            'hasil_pelaksanaan' => 'Telah dilakukan workshop dan implementasi sistem.',
            'tautan_kegiatan' => 'https://example.com/kegiatan-ai',
        ];

        $this->post(route('pelaksanaankerjasama.store'), $this->payload);
        $this->data = PelaksanaanKerjaSama::where('idrekap', $this->rekap->id)->first();
    }

    public function test_ruang_lingkup_berhasil_terinput()
    {
        $this->assertEquals(
            $this->payload['ruang_lingkup'],
            $this->data->ruang_lingkup,
            'ruang_lingkup berhasil diinput'
        );
    }

    public function test_dosen_terlibat_berhasil_terinput()
    {
        $this->assertEquals(
            $this->payload['dosen_terlibat'],
            $this->data->dosen_terlibat,
            'dosen_terlibat berhasil diinput'
        );
    }

    public function test_mahasiswa_terlibat_berhasil_terinput()
    {
        $this->assertEquals(
            $this->payload['mahasiswa_terlibat'],
            $this->data->mahasiswa_terlibat,
            'mahasiswa_terlibat berhasil diinput'
        );
    }

    public function test_anggaran_ukdw_berhasil_terinput()
    {
        $this->assertEquals(
            (string) $this->payload['anggaran_ukdw'],
            $this->data->anggaran_ukdw,
            'anggaran_ukdw berhasil diinput'
        );
    }

    public function test_hasil_pelaksanaan_berhasil_terinput()
    {
        $this->assertEquals(
            $this->payload['hasil_pelaksanaan'],
            $this->data->hasil_pelaksanaan,
            'hasil_pelaksanaan berhasil diinput'
        );
    }

    public function test_tautan_kegiatan_berhasil_terinput()
    {
        $this->assertEquals(
            $this->payload['tautan_kegiatan'],
            $this->data->tautan_link_kegiatan,
            'tautan_kegiatan berhasil diinput'
        );
    }
}
