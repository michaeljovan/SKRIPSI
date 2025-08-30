<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiKinerjaOtp;

class RekapKerjaSamaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Mail::fake();
        $this->startSession();

        // Wajib login karena routes berada di group auth
        $this->actingAs(User::factory()->create());

        // Matikan middleware yang tidak relevan untuk test UI
        $this->withoutMiddleware([
            \App\Http\Middleware\cekrole::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);
    }

    /** Helper buat rekap minimal valid */
    private function makeRekap(array $override = []): RekapKerjaSama
    {
        $base = [
            'no_dokumen'          => 'DOC-' . Str::upper(Str::random(5)),
            'unit'                => 'FTI',
            'mitra_kerja_sama'    => 'PT Contoh',
            'judul_kerja_sama'    => 'Judul Kerma',
            'bentuk_kerja_sama'   => 'Pendidikan, Penelitian',
            'jenis_kerja_sama'    => 'MoU',
            'pihak_ukdw'          => 'FTI UKDW',
            'pihak_mitra'         => 'PT Contoh',
            'email_pihak_mitra'   => 'mitra@example.com',
            'tanggal_mulai'       => now()->subDays(2)->toDateString(),
            'tanggal_selesai'     => now()->addDays(5)->toDateString(),
            'masa_berlaku'        => 8,
            'kategori'            => 'nasional',
            'dokumen_path'        => 'dokumen_kerja_sama/dummy.pdf',
            'status'              => 'aktif',
            'is_laporan'          => false,
            'is_kinerja'          => false,
            'is_mitra'            => false,
        ];

        return RekapKerjaSama::create(array_merge($base, $override));
    }

    /** ========== INDEX ========== */
    /** @test */
    public function index_menampilkan_view_datadokumenkerjasama()
    {
        $this->makeRekap();
        $resp = $this->get(route('data_kerja_sama'));
        $resp->assertStatus(200)
             ->assertViewIs('datadokumenkerjasama')
             ->assertViewHas('rekapKerjaSama');
    }

    /** ========== STORE (JSON) ========== */
    /** @test */
    public function store_json_berhasil_menyimpan_pdf_dan_return_redirect_url()
    {
        $payload = [
            'noDokumen'         => 'BARU-001',
            'unit'              => 'FTI',
            'mitraKerjaSama'    => 'PT A',
            'judulKerjaSama'    => 'Proyek A',
            'bentukKerjaSama'   => ['Penelitian', 'Pendidikan'],
            'jenisKerjaSama'    => 'MoU',
            'jenisPermohonan'   => 'baru',
            'pihakUKDW'         => 'FTI',
            'pihakMitra'        => 'PT A',
            'emailMitra'        => 'a@example.com',
            'tanggalMulai'      => now()->toDateString(),
            'tanggalSelesai'    => now()->addDays(10)->toDateString(),
            'kategori'          => 'nasional',
            'inKind'            => '1.234,56',
            'inCash'            => '2.000',
            'totalInKind'       => '1.234,56',
            'totalInCash'       => '2.000',
            'jumlahImplementasi'=> 3,
            'dokumenPendukung'  => UploadedFile::fake()->create('dok.pdf', 120, 'application/pdf'),
        ];

        $resp = $this->postJson(route('rekapkerjasama.store'), $payload);
        $resp->assertStatus(200)
             ->assertJson([
                 'success'  => true,
                 'redirect' => route('data_kerja_sama'),
             ]);

        $this->assertDatabaseHas('rekapkerjasama', [
            'no_dokumen'       => 'BARU-001',
            'mitra_kerja_sama' => 'PT A',
        ]);

        $row = RekapKerjaSama::where('no_dokumen','BARU-001')->first();
        Storage::disk('public')->assertExists($row->dokumen_path);
    }

    /** @test */
    public function store_json_validasi_gagal_422()
    {
        $resp = $this->postJson(route('rekapkerjasama.store'), [
            // hilangkan banyak field wajib supaya gagal
            'noDokumen'       => '',
            'jenisKerjaSama'  => 'MoU',
            'jenisPermohonan' => 'baru',
            'dokumenPendukung'=> UploadedFile::fake()->create('dok.pdf', 10, 'application/pdf'),
        ]);

        $resp->assertStatus(422)->assertJsonStructure(['success','message','errors']);
    }

    /** ========== UPDATE ========== */
    /** @test */
    public function update_berhasil_memperbarui_field_dan_replace_file_dengan_status_aktif()
    {
        $rekap = $this->makeRekap(['dokumen_path' => 'dokumen_kerja_sama/old.pdf']);
        Storage::disk('public')->put('dokumen_kerja_sama/old.pdf', 'OLD');

        $payload = [
            'noDokumen'         => 'BARU-002',
            'unit'              => 'FTI',
            'mitraKerjaSama'    => 'PT B',
            'judulKerjaSama'    => 'Proyek B',
            'bentukKerjaSama'   => ['Penelitian'],
            'jenisKerjaSama'    => 'MoU',
            'pihakUKDW'         => 'FTI',
            'pihakMitra'        => 'PT B',
            'emailMitra'        => 'b@example.com',
            // set selesai di masa depan -> status aktif
            'tanggalMulai'      => now()->toDateString(),
            'tanggalSelesai'    => now()->addDays(5)->toDateString(),
            'kategori'          => 'nasional',
            'dokumenPendukung'  => UploadedFile::fake()->create('baru.pdf', 80, 'application/pdf'),
        ];

        $resp = $this->put(route('rekapkerjasama.update', ['id' => $rekap->id]), $payload);
        $resp->assertRedirect(route('data_kerja_sama'));

        $rekap->refresh();
        $this->assertSame('BARU-002', $rekap->no_dokumen);
        $this->assertSame('PT B', $rekap->pihak_mitra);
        $this->assertSame('aktif', $rekap->status);

        Storage::disk('public')->assertMissing('dokumen_kerja_sama/old.pdf');
        Storage::disk('public')->assertExists($rekap->dokumen_path);
    }

    /** @test */
    public function update_menetapkan_status_selesai_bila_tanggal_selesai_lampau()
    {
        $rekap = $this->makeRekap();

        // Bekukan waktu "now"
        Carbon::setTestNow(Carbon::parse('2025-08-27'));
        $payload = [
            'noDokumen'         => 'BARU-003',
            'unit'              => 'FTI',
            'mitraKerjaSama'    => 'PT C',
            'judulKerjaSama'    => 'Proyek C',
            'bentukKerjaSama'   => ['Penelitian'],
            'jenisKerjaSama'    => 'MoU',
            'pihakUKDW'         => 'FTI',
            'pihakMitra'        => 'PT C',
            'emailMitra'        => 'c@example.com',
            'tanggalMulai'      => '2025-08-01',
            'tanggalSelesai'    => '2025-08-10', // < now (27) -> selesai
            'kategori'          => 'nasional',
        ];

        $this->put(route('rekapkerjasama.update', ['id' => $rekap->id]), $payload)
             ->assertRedirect(route('data_kerja_sama'));

        $rekap->refresh();
        $this->assertSame('selesai', $rekap->status);

        Carbon::setTestNow(); // clear
    }

    /** ========== getDokumenInduk & options ========== */
    /** @test */
    public function get_dokumen_induk_moa_mengembalikan_hanya_mou()
    {
        $mou = $this->makeRekap(['jenis_kerja_sama' => 'MoU']);
        $moa = $this->makeRekap(['jenis_kerja_sama' => 'MoA']);

        $resp = $this->getJson(route('api.dokumen_induk', ['jenis' => 'MoA']));
        $resp->assertStatus(200);

        $data = $resp->json();
        $ids  = collect($data)->pluck('id')->all();

        $this->assertContains($mou->id, $ids);
        $this->assertNotContains($moa->id, $ids);
        $this->assertSame('none', (string) $data[0]['id']); // opsi "Tidak Ada Induk" di awal
    }

    /** @test */
    public function get_dokumen_induk_ia_mengembalikan_mou_dan_moa()
    {
        $mou = $this->makeRekap(['jenis_kerja_sama' => 'MoU']);
        $moa = $this->makeRekap(['jenis_kerja_sama' => 'MoA']);

        $resp = $this->getJson(route('api.dokumen_induk', ['jenis' => 'IA']));
        $resp->assertStatus(200);

        $ids = collect($resp->json())->pluck('id')->all();
        $this->assertContains($mou->id, $ids);
        $this->assertContains($moa->id, $ids);
    }

    /** @test */
    public function get_dokumen_induk_jenis_tidak_valid_mengembalikan_400()
    {
        $this->getJson(route('api.dokumen_induk', ['jenis' => 'ABC']))
             ->assertStatus(400);
    }


    /** ========== lihatPDF ========== */
    /** @test */
    public function lihat_pdf_200_jika_file_ada()
    {
        $rekap = $this->makeRekap(['dokumen_path' => 'dokumen_kerja_sama/sample.pdf']);
        Storage::disk('public')->put('dokumen_kerja_sama/sample.pdf', 'PDFBYTES');

        $resp = $this->get(route('rekapkerjasama.pdf', ['id' => $rekap->id]));
        $resp->assertStatus(200);
        $this->assertTrue(str_contains($resp->headers->get('Content-Type'), 'application/pdf'));
    }

    /** @test */
    public function lihat_pdf_404_jika_file_tidak_ada()
    {
        $rekap = $this->makeRekap(['dokumen_path' => 'dokumen_kerja_sama/tidakada.pdf']);
        $this->get(route('rekapkerjasama.pdf', ['id' => $rekap->id]))
             ->assertStatus(404);
    }

    /** ========== stopForm & stop ========== */
    /** @test */
    public function stop_form_menampilkan_view_dengan_preview_durasi()
    {
        Carbon::setTestNow('2025-08-27');

        $rekap = $this->makeRekap([
            'tanggal_mulai'   => '2025-08-20',
            'tanggal_selesai' => '2025-08-30',
            'status'          => 'aktif',
        ]);

        $this->get(route('rekapkerjasama.stop.form', ['id' => $rekap->id]))
             ->assertStatus(200)
             ->assertViewIs('stopkerjasama')
             ->assertViewHas('rekap');

        Carbon::setTestNow();
    }

    /** @test */
    public function stop_mengembalikan_info_jika_sudah_selesai_or_dihentikan()
    {
        // Skenario sudah selesai
        $rekap1 = $this->makeRekap([
            'tanggal_mulai'   => now()->subDays(10)->toDateString(),
            'tanggal_selesai' => now()->subDay()->toDateString(),
            'status'          => 'selesai',
        ]);

        $this->patch(route('rekapkerjasama.stop', ['id' => $rekap1->id]), [
            'alasan' => 'apapun',
        ])->assertRedirect(route('data_kerja_sama'))
          ->assertSessionHas('info');

        // Skenario sudah dihentikan
        $rekap2 = $this->makeRekap(['status' => 'dihentikan']);
        $this->patch(route('rekapkerjasama.stop', ['id' => $rekap2->id]), [
            'alasan' => 'apapun',
        ])->assertRedirect(route('data_kerja_sama'))
          ->assertSessionHas('info');
    }

    /** ========== delete ========== */
    /** @test */
    public function delete_menghapus_rekap_dan_file_pdf()
    {
        $rekap = $this->makeRekap(['dokumen_path' => 'dokumen_kerja_sama/hapus.pdf']);
        Storage::disk('public')->put('dokumen_kerja_sama/hapus.pdf', 'pdf');

        $resp = $this->delete(route('rekapkerjasama.delete', ['id' => $rekap->id]));
        $resp->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseMissing('rekapkerjasama', ['id' => $rekap->id]);
        Storage::disk('public')->assertMissing('dokumen_kerja_sama/hapus.pdf');
    }

    /** @test */
    public function delete_404_jika_id_tidak_ditemukan()
    {
        $this->delete(route('rekapkerjasama.delete', ['id' => 99999]))
             ->assertStatus(404);
    }

    /** ========== cekNoDokumen (sekadar sanity) ========== */
    /** @test */
    public function cek_no_dokumen_mengembalikan_exists_boolean()
    {
        $this->makeRekap(['no_dokumen' => 'EXIST-1']);

        $this->get(route('cek.no_dokumen', ['no_dokumen' => 'EXIST-1']))
             ->assertStatus(200)
             ->assertJson(['exists' => true]);

        $this->get(route('cek.no_dokumen', ['no_dokumen' => 'NOTEXIST']))
             ->assertStatus(200)
             ->assertJson(['exists' => false]);
    }
}
