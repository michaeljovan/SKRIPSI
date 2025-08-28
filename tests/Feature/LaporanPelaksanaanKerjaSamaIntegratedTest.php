<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\RekapKerjaSama;
use App\Models\PelaksanaanKerjaSama;

class LaporanPelaksanaanKerjaSamaIntegratedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Simulasi disk public untuk upload/hapus file
        Storage::fake('public');

        // Pastikan session aktif agar $errors tersedia di Blade
        $this->startSession();

        // Login sebagai user biasa (route berada di dalam auth)
        $this->actingAs(User::factory()->create());

        // Matikan middleware yang tidak relevan untuk test
        $this->withoutMiddleware([
            \App\Http\Middleware\cekrole::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);
    }

    /** Helper buat Rekap minimal valid */
    private function makeRekap(array $override = []): RekapKerjaSama
    {
        $base = [
            'no_dokumen'          => 'DOC-' . Str::upper(Str::random(5)),
            'unit'                => 'FTI',
            'mitra_kerja_sama'    => 'PT Contoh',
            'judul_kerja_sama'    => 'Kegiatan A',
            'bentuk_kerja_sama'   => 'Pendidikan, Penelitian',
            'jenis_kerja_sama'    => 'MoU',
            'pihak_ukdw'          => 'FTI UKDW',
            'pihak_mitra'         => 'PT Contoh',
            'email_pihak_mitra'   => 'mitra@example.com',
            'tanggal_mulai'       => now()->subDays(3)->toDateString(),
            'tanggal_selesai'     => now()->addDays(10)->toDateString(),
            'masa_berlaku'        => 14,
            'kategori'            => 'nasional',
            'dokumen_path'        => 'dokumen_kerja_sama/x.pdf',
            'status'              => 'aktif',
            'is_laporan'          => false,
            'is_kinerja'          => false,
            'is_mitra'            => false,
        ];

        return RekapKerjaSama::create(array_merge($base, $override));
    }

    /** ---------- CREATE ---------- */
    /** @test */
    public function create_menampilkan_form_dengan_rekap()
    {
        $rekap = $this->makeRekap();

        $resp = $this->get(route('pelaksanaankerjasama.create', ['id' => $rekap->id]));
        $resp->assertStatus(200)
             ->assertViewIs('inputlaporanpelaksanaankerjasama')
             ->assertViewHas('rekap', fn ($r) => $r->id === $rekap->id);
    }

    /** ---------- INDEX ---------- */
    /** @test */
    public function index_hanya_menampilkan_rekap_yang_memiliki_pelaksanaan()
    {
        $rek1 = $this->makeRekap();
        $rek2 = $this->makeRekap(['no_dokumen' => 'DOC-XYZ99']);

        // punya laporan
        PelaksanaanKerjaSama::create([
            'idrekap'                    => $rek1->id,
            'ruang_lingkup'              => 'Pelaksanaan A',
            'jumlah_dosen_terlibat'      => 1,
            'jumlah_mahasiswa_terlibat'  => 2,
            'dosen_terlibat'             => 'D1',
            'mahasiswa_terlibat'         => 'M1',
            'anggaran_ukdw'              => 1000,
            'hasil_pelaksanaan'          => 'OK',
            'tautan_link_kegiatan'       => '',      // hindari null kalau kolom NOT NULL
            'dokumen_kegiatan'           => null,
        ]);

        $resp = $this->get(route('pelaksanaankerjasama.index'));
        $resp->assertStatus(200)
             ->assertViewIs('laporanpelaksanaankerjasama')
             ->assertViewHas('rekap', function ($paginator) use ($rek1, $rek2) {
                 $ids = collect($paginator->items())->pluck('id')->all();
                 return in_array($rek1->id, $ids) && !in_array($rek2->id, $ids);
             });
    }

    /** ---------- STORE (redirect) ---------- */
    /** @test */
    public function store_berhasil_redirect_set_is_laporan_true_dan_upload_file()
    {
        $rekap = $this->makeRekap();

        $payload = [
            'rekap_id'                   => $rekap->id,
            'ruang_lingkup'              => 'Ruang X',
            'jumlah_dosen_terlibat'      => 2,
            'jumlah_mahasiswa_terlibat'  => 3,
            'dosen_terlibat'             => 'A,B',
            'mahasiswa_terlibat'         => 'C,D,E',
            'anggaran_ukdw'              => 250000,
            'hasil_pelaksanaan'          => 'Selesai dengan baik',
            'tautan_link_kegiatan'       => 'https://example.com/kegiatan',
            'dokumen_kegiatan'           => UploadedFile::fake()->create('keg.pdf', 120, 'application/pdf'),
        ];

        $resp = $this->post(route('pelaksanaankerjasama.store'), $payload);
        $resp->assertRedirect(route('pelaksanaankerjasama.index'));

        // tersimpan
        $row = PelaksanaanKerjaSama::first();
        $this->assertNotNull($row);
        $this->assertSame($rekap->id, $row->idrekap);
        $this->assertSame('Ruang X', $row->ruang_lingkup);
        $this->assertSame(250000, (int) $row->anggaran_ukdw);
        $this->assertNotNull($row->dokumen_kegiatan);
        Storage::disk('public')->assertExists($row->dokumen_kegiatan);

        // flag rekap berubah
        $rekap->refresh();
        $this->assertTrue((bool) $rekap->is_laporan);
    }

    /** ---------- STORE (JSON) ---------- */
    /** @test */
    public function store_json_berhasil_200_dan_message()
    {
        $rekap = $this->makeRekap();

        $payload = [
            'rekap_id'                   => $rekap->id,
            'ruang_lingkup'              => 'Ruang JSON',
            'jumlah_dosen_terlibat'      => 1,
            'jumlah_mahasiswa_terlibat'  => 1,
            'dosen_terlibat'             => 'Dosen A',
            'mahasiswa_terlibat'         => 'Mhs A',
            'anggaran_ukdw'              => 5000,
            'hasil_pelaksanaan'          => 'Bagus',
            'tautan_link_kegiatan'       => 'https://example.com/x',
            'dokumen_kegiatan'           => UploadedFile::fake()->create('dok.pdf', 90, 'application/pdf'),
        ];

        // Kirim header Accept JSON agar controller mengembalikan JSON
        $resp = $this->post(route('pelaksanaankerjasama.store'), $payload, ['Accept' => 'application/json']);
        $resp->assertStatus(200)->assertJson(['message' => 'Laporan pelaksanaan berhasil disimpan']);

        $row = PelaksanaanKerjaSama::latest()->first();
        $this->assertSame('Ruang JSON', $row->ruang_lingkup);
        Storage::disk('public')->assertExists($row->dokumen_kegiatan);

        $rekap->refresh();
        $this->assertTrue((bool) $rekap->is_laporan);
    }

    /** ---------- STORE (validasi gagal JSON) ---------- */
    /** @test */
    public function store_json_validasi_gagal_422()
    {
        $rekap = $this->makeRekap();

        $payload = [
            'rekap_id'          => $rekap->id,
            // 'ruang_lingkup'   => missing
            'anggaran_ukdw'     => 'abc', // tidak numeric
            'hasil_pelaksanaan' => '',    // kosong
        ];

        $resp = $this->post(route('pelaksanaankerjasama.store'), $payload, ['Accept' => 'application/json']);
        $resp->assertStatus(422)
             ->assertJsonStructure(['errors']);
    }

    /** ---------- EDIT ---------- */
    /** @test */
    public function edit_menampilkan_form_dengan_data_pelaksanaan_dan_rekap()
    {
        $rekap = $this->makeRekap();
        $pel = PelaksanaanKerjaSama::create([
            'idrekap'                    => $rekap->id,
            'ruang_lingkup'              => 'Pelaksanaan X',
            'jumlah_dosen_terlibat'      => 2,
            'jumlah_mahasiswa_terlibat'  => 2,
            'dosen_terlibat'             => 'A,B',
            'mahasiswa_terlibat'         => 'C,D',
            'anggaran_ukdw'              => 200000,
            'hasil_pelaksanaan'          => 'Baik',
            'tautan_link_kegiatan'       => '',      // hindari null
            'dokumen_kegiatan'           => null,
        ]);

        $resp = $this->get(route('pelaksanaankerjasama.edit', ['id' => $pel->id]));
        $resp->assertStatus(200)
             ->assertViewIs('editpelaksanaankerjasama')
             ->assertViewHas('pelaksanaan', fn($p) => $p->id === $pel->id)
             ->assertViewHas('rekap', fn($r) => $r->id === $rekap->id);
    }

    /** ---------- UPDATE ---------- */
    /** @test */
    public function update_mengganti_file_jika_diunggah_dan_memperbarui_data()
    {
        $rekap = $this->makeRekap();

        // Buat file awal
        Storage::disk('public')->put('dokumen_kegiatan/old.pdf', 'OLD');
        $pel = PelaksanaanKerjaSama::create([
            'idrekap'                    => $rekap->id,
            'ruang_lingkup'              => 'Awal',
            'jumlah_dosen_terlibat'      => 1,
            'jumlah_mahasiswa_terlibat'  => 1,
            'dosen_terlibat'             => 'Dosen A',
            'mahasiswa_terlibat'         => 'Mhs A',
            'anggaran_ukdw'              => 1000,
            'hasil_pelaksanaan'          => 'OK',
            'tautan_link_kegiatan'       => '',
            'dokumen_kegiatan'           => 'dokumen_kegiatan/old.pdf',
        ]);

        $resp = $this->put(route('pelaksanaankerjasama.update', ['id' => $pel->id]), [
            'ruang_lingkup'              => 'Baru',
            'jumlah_dosen_terlibat'      => 3,
            'jumlah_mahasiswa_terlibat'  => 4,
            'dosen_terlibat'             => 'Dosen X, Dosen Y',
            'mahasiswa_terlibat'         => 'Mhs X, Mhs Y, Mhs Z, Mhs W',
            'anggaran_ukdw'              => 2000,
            'hasil_pelaksanaan'          => 'Lebih baik',
            // biarkan tautan kosong (nullable)
            'dokumen_kegiatan'           => UploadedFile::fake()->create('baru.pdf', 80, 'application/pdf'),
        ]);

        $resp->assertRedirect(route('pelaksanaankerjasama.index'));

        $pel->refresh();
        $this->assertSame('Baru', $pel->ruang_lingkup);
        $this->assertSame(2000, (int) $pel->anggaran_ukdw);
        $this->assertNotNull($pel->dokumen_kegiatan);

        // file lama terhapus, file baru ada
        Storage::disk('public')->assertMissing('dokumen_kegiatan/old.pdf');
        Storage::disk('public')->assertExists($pel->dokumen_kegiatan);
    }

    /** ---------- DESTROY ---------- */
    /** @test */
    public function destroy_menghapus_pelaksanaan_dan_menyetel_is_laporan_false()
    {
        $rekap = $this->makeRekap(['is_laporan' => true]);

        $pel = PelaksanaanKerjaSama::create([
            'idrekap'                    => $rekap->id,
            'ruang_lingkup'              => 'A',
            'jumlah_dosen_terlibat'      => 1,
            'jumlah_mahasiswa_terlibat'  => 1,
            'dosen_terlibat'             => 'D1',
            'mahasiswa_terlibat'         => 'M1',
            'anggaran_ukdw'              => 100,
            'hasil_pelaksanaan'          => 'ok',
            'tautan_link_kegiatan'       => '',
            'dokumen_kegiatan'           => null,
        ]);

        $resp = $this->delete(route('pelaksanaankerjasama.destroy', ['id' => $pel->id]));
        $resp->assertRedirect(route('pelaksanaankerjasama.index'));

        $this->assertDatabaseMissing('pelaksanaankerjasama', ['id' => $pel->id]);

        $rekap->refresh();
        $this->assertFalse((bool) $rekap->is_laporan);
    }
}
