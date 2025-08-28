<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\RekapKerjaSama;
use App\Models\PelaksanaanKerjaSama;

class LaporanPelaksanaanKerjaSamaUnitTest extends TestCase
{
    use RefreshDatabase;

    /** Helper: buat 1 rekap minimal valid */
    private function makeRekap(array $override = []): RekapKerjaSama
    {
        $base = [
            'no_dokumen'        => 'DOC-' . Str::upper(Str::random(5)),
            'unit'              => 'Informatika',
            'mitra_kerja_sama'  => 'PT Contoh',
            'judul_kerja_sama'  => 'Judul Kegiatan',
            'bentuk_kerja_sama' => 'Pendidikan',
            'jenis_kerja_sama'  => 'MoU',
            'pihak_ukdw'        => 'FTI',
            'pihak_mitra'       => 'PT Contoh',
            'email_pihak_mitra' => 'mitra@example.com',
            'tanggal_mulai'     => now()->subDays(2)->toDateString(),
            'tanggal_selesai'   => now()->addDays(10)->toDateString(),
            'masa_berlaku'      => 13,
            'kategori'          => 'nasional',
            'dokumen_path'      => 'dummy.pdf',
            'is_laporan'        => false,
            'is_kinerja'        => false,
            'is_mitra'          => false,
        ];

        return RekapKerjaSama::create(array_merge($base, $override));
    }

    /** @test */
    public function index_hanya_menampilkan_rekap_yang_memiliki_laporan()
    {
        $rekapTanpa = $this->makeRekap();
        $rekapDengan = $this->makeRekap(['no_dokumen' => 'DOC-ADA']);

        PelaksanaanKerjaSama::create([
            'idrekap'                   => $rekapDengan->id,
            'ruang_lingkup'             => 'Pelaksanaan A',
            'jumlah_dosen_terlibat'     => 2,
            'jumlah_mahasiswa_terlibat' => 3,
            'dosen_terlibat'            => 'Dosen A, Dosen B',
            'mahasiswa_terlibat'        => 'Mhs A, Mhs B, Mhs C',
            'anggaran_ukdw'             => 100000,
            'hasil_pelaksanaan'         => 'Hasil ok',
            'tautan_link_kegiatan'      => 'https://example.com',
            'dokumen_kegiatan'          => null,
        ]);

        $this->withoutMiddleware();
        $resp = $this->get(route('pelaksanaankerjasama.index'));

        $resp->assertStatus(200);
        $resp->assertViewIs('laporanpelaksanaankerjasama');
        $resp->assertViewHas('rekap');

        // Harus terlihat no dokumen rekap yang punya laporan
        $resp->assertSee('DOC-ADA');

        // Yang tidak punya laporan idealnya tidak muncul
        // (Jika view tidak menampilkan no_dokumen, baris di bawah bisa dihapus)
        $resp->assertDontSee($rekapTanpa->no_dokumen);
    }

    /** @test */
    public function store_menyimpan_laporan_mengunggah_pdf_dan_set_is_laporan_true()
    {
        Storage::fake('public');

        $rekap = $this->makeRekap();

        $payload = [
            'rekap_id'                  => $rekap->id,
            'ruang_lingkup'             => 'Ruang lingkup kegiatan',
            'jumlah_dosen_terlibat'     => 1,
            'jumlah_mahasiswa_terlibat' => 2,
            'dosen_terlibat'            => "Dr. A\nDr. B",
            'mahasiswa_terlibat'        => "Mhs 1, Mhs 2",
            'anggaran_ukdw'             => '1234567', // numeric valid
            'hasil_pelaksanaan'         => 'Semua berjalan baik',
            'tautan_link_kegiatan'      => 'https://example.com/kegiatan',
            'dokumen_kegiatan'          => UploadedFile::fake()->create('kegiatan.pdf', 120, 'application/pdf'),
        ];

        $this->withoutMiddleware();
        $resp = $this->post(route('pelaksanaankerjasama.store'), $payload);

        $resp->assertRedirect(route('pelaksanaankerjasama.index'));

        // Pastikan tersimpan di DB
        $this->assertDatabaseHas('pelaksanaankerjasama', [
            'idrekap'           => $rekap->id,
            'ruang_lingkup'     => 'Ruang lingkup kegiatan',
            'anggaran_ukdw'     => 1234567,
            'hasil_pelaksanaan' => 'Semua berjalan baik',
        ]);

        // Pastikan file tersimpan di disk public
        $row = PelaksanaanKerjaSama::where('idrekap', $rekap->id)->first();
        $this->assertNotNull($row);
        if ($row->dokumen_kegiatan) {
            Storage::disk('public')->assertExists($row->dokumen_kegiatan);
        }

        // is_laporan pada rekap menjadi true
        $rekap->refresh();
        $this->assertTrue((bool) $rekap->is_laporan);
    }

    /** @test */
    public function store_validasi_gagal_mengembalikan_error()
    {
        $rekap = $this->makeRekap();

        $this->withoutMiddleware();
        $resp = $this->from(route('pelaksanaankerjasama.create', ['id' => $rekap->id]))
            ->post(route('pelaksanaankerjasama.store'), [
                // kosongkan banyak field wajib
                'rekap_id' => '', // required
            ]);

        $resp->assertRedirect(); // kembali dengan error
        $resp->assertSessionHasErrors(['rekap_id', 'ruang_lingkup', 'anggaran_ukdw', 'hasil_pelaksanaan']);
    }
}
