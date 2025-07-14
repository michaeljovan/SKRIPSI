<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RekapKerjaSamaTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'staff',
        ]);

        $this->actingAs($this->user);

        Gate::define('access-rekap', fn($user) => in_array($user->role, ['staff', 'dekan']));

        Storage::fake('public');

        $this->file = UploadedFile::fake()->create('dokumen_pendukung.pdf', 100, 'application/pdf');
    }

    private function postRekap()
    {
        return $this->post(route('rekapkerjasama.store'), [
            'noDokumen' => 'DOC-2025-001',
            'unit' => 'Fakultas Teknologi Informasi',
            'mitraKerjaSama' => 'PT Mitra AI',
            'judulKerjaSama' => 'Pengembangan Sistem Cerdas',
            'bentukKerjaSama' => ['Penelitian', 'Pendidikan'],
            'jenisKerjaSama' => 'MoU',
            'pihakUKDW' => 'UKDW',
            'pihakMitra' => 'PT Mitra AI',
            'tanggalMulai' => '2025-01-01',
            'tanggalSelesai' => '2025-12-31',
            'kategori' => 'nasional',
            'inKind' => 'Perangkat lunak',
            'totalInKind' => 1000000,
            'inCash' => 500000,
            'totalInCash' => 1500000,
            'jumlahImplementasi' => 3,
            'dokumenPendukung' => $this->file,
        ]);
    }

    public function test_no_dokumen_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'no_dokumen' => 'DOC-2025-001',
        ]);
    }

    public function test_unit_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'unit' => 'Fakultas Teknologi Informasi',
        ]);
    }

    public function test_mitra_kerja_sama_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'mitra_kerja_sama' => 'PT Mitra AI',
        ]);
    }

    public function test_judul_kerja_sama_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'judul_kerja_sama' => 'Pengembangan Sistem Cerdas',
        ]);
    }

    public function test_bentuk_kerja_sama_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'bentuk_kerja_sama' => 'Penelitian, Pendidikan',
        ]);
    }

    public function test_jenis_kerja_sama_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'jenis_kerja_sama' => 'MoU',
        ]);
    }

    public function test_pihak_ukdw_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'pihak_ukdw' => 'UKDW',
        ]);
    }

    public function test_pihak_mitra_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'pihak_mitra' => 'PT Mitra AI',
        ]);
    }

    public function test_tanggal_mulai_dan_selesai_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-12-31',
        ]);
    }

    public function test_kategori_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'kategori' => 'nasional',
        ]);
    }

    public function test_in_kind_dan_total_in_kind_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'in_kind' => 'Perangkat lunak',
            'total_in_kind' => 1000000,
        ]);
    }

    public function test_in_cash_dan_total_in_cash_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'in_cash' => 500000,
            'total_in_cash' => 1500000,
        ]);
    }

    public function test_jumlah_implementasi_berhasil_diinput()
    {
        $this->postRekap();

        $this->assertDatabaseHas('rekapkerjasama', [
            'jumlah_implementasi' => 3,
        ]);
    }

    public function test_dokumen_pendukung_file_tersimpan()
    {
        $this->postRekap();

        Storage::disk('public')->assertExists('dokumen_kerja_sama/' . $this->file->hashName());
    }
}
