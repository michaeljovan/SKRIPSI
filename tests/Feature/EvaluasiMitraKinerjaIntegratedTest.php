<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitraKinerja;
use App\Models\EvaluasiKinerjaOtp;
use App\Models\User;

class EvaluasiMitraKinerjaIntegratedTest extends TestCase
{
    use RefreshDatabase;

    /** ---------- Helpers ---------- */

    protected function makeRekap(array $override = []): RekapKerjaSama
    {
        $base = [
            'no_dokumen'          => 'DOC-' . Str::upper(Str::random(5)),
            'unit'                => 'FTI',
            'mitra_kerja_sama'    => 'PT Kinerja',
            'judul_kerja_sama'    => 'Penelitian AI',
            'bentuk_kerja_sama'   => 'Pendidikan, Penelitian',
            'jenis_kerja_sama'    => 'MoU',
            'pihak_ukdw'          => 'FTI UKDW',
            'pihak_mitra'         => 'PT Kinerja',
            'email_pihak_mitra'   => 'mitra@example.com',
            'tanggal_mulai'       => now()->subDays(2)->toDateString(),
            'tanggal_selesai'     => now()->addDays(5)->toDateString(),
            'masa_berlaku'        => 8,
            'kategori'            => 'nasional',
            'dokumen_path'        => 'dokumen_kerja_sama/abc.pdf',
            'status'              => 'aktif',
            'is_laporan'          => false,
            'is_kinerja'          => false,
            'is_mitra'            => false,
        ];

        return RekapKerjaSama::create(array_merge($base, $override));
    }

    /** ---------- Boot per test ---------- */
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Mail::fake();
        $this->startSession(); // supaya $errors tersedia di Blade
    }

    /** ---------- OTP Gate & Verify ---------- */

    /** @test */
    public function otp_gate_view_muncul()
    {
        $rekap = $this->makeRekap();

        $resp = $this->get(route('EvaluasiMitraKinerja.otpGate', ['rekapId' => $rekap->id]));
        $resp->assertStatus(200)->assertViewIs('evaluasi_kinerja_otp_gate');
    }

    /** @test */
    public function verify_otp_sukses_set_session_dan_redirect_ke_create()
    {
        $rekap = $this->makeRekap();

        EvaluasiKinerjaOtp::create([
            'rekap_id'      => $rekap->id,
            'code_hash'     => \Hash::make('123456'),
            'expires_at'    => now()->addMinutes(30),
            'used_at'       => null,
            'sent_to_email' => 'admin@example.com',
        ]);

        $resp = $this->post(route('EvaluasiMitraKinerja.verifyOtp', ['rekapId' => $rekap->id]), [
            'otp' => '123456',
        ]);

        $resp->assertRedirect(route('EvaluasiMitraKinerja.create', ['id' => $rekap->id]));
        $this->assertEquals($rekap->id, (int) session('evaluasi_mitra_kinerja_allowed'));
    }

    /** @test */
    public function verify_otp_gagal_kode_salah()
    {
        $rekap = $this->makeRekap();

        EvaluasiKinerjaOtp::create([
            'rekap_id'      => $rekap->id,
            'code_hash'     => \Hash::make('111111'),
            'expires_at'    => now()->addMinutes(30),
            'used_at'       => null,
            'sent_to_email' => 'admin@example.com',
        ]);

        $resp = $this->from(route('EvaluasiMitraKinerja.otpGate', ['rekapId' => $rekap->id]))
            ->post(route('EvaluasiMitraKinerja.verifyOtp', ['rekapId' => $rekap->id]), [
                'otp' => '222222',
            ]);

        $resp->assertRedirect(route('EvaluasiMitraKinerja.otpGate', ['rekapId' => $rekap->id]));
        $resp->assertSessionHasErrors('otp');
    }

    /** ---------- Create (gate) ---------- */

    /** @test */
    public function create_wajib_via_otp_gate_jika_session_tidak_sah()
    {
        $rekap = $this->makeRekap();

        // PAKAI URL PUBLIK untuk menghindari bentrok nama route dengan yang di grup auth
        $resp = $this->get('/evaluasi-mitra-kinerja/'.$rekap->id.'/create');
        $resp->assertRedirect(route('EvaluasiMitraKinerja.otpGate', ['rekapId' => $rekap->id]))
             ->assertSessionHas('error');
    }

    /** @test */
    public function create_ok_saat_session_valid()
    {
        $rekap = $this->makeRekap();

        session(['evaluasi_mitra_kinerja_allowed' => $rekap->id]);

        // PAKAI URL PUBLIK
        $resp = $this->get('/evaluasi-mitra-kinerja/'.$rekap->id.'/create');
        $resp->assertStatus(200)
             ->assertViewIs('inputevaluasikerjasamakinerja')
             ->assertViewHasAll(['rekap','dosenCount','mahasiswaCount']);
    }

    /** ---------- Store ---------- */

    /** @test */
    public function store_menyimpan_mapping_upload_pdf_dan_set_is_kinerja_true()
    {
        // STORE berada di grup auth → login dulu
        $this->actingAs(User::factory()->create());

        $rekap = $this->makeRekap();
        session(['evaluasi_mitra_kinerja_allowed' => $rekap->id]); // gate OTP

        $payload = [
            'rekap_id'         => $rekap->id,
            'nodok'            => $rekap->no_dokumen,
            'mitra'            => $rekap->mitra_kerja_sama,
            'pengisi_mitra'    => 'Ibu Penilai',
            'integritas'       => 'Tinggi',
            'keahlian'         => 'Cukup',
            'komunikasi'       => 'Sangat Tinggi',
            'kerjasamatim'     => 'Tinggi',
            'pengembangandiri' => 'Cukup',
            'kreativitas'      => 'Kurang',
            'bahasaasing'      => 'Sangat Kurang',
            'teknologi'        => 'Tinggi',
            'manajerial'       => 'Cukup',
            'analisis'         => 'Tinggi',
            'laporan'          => 'Sangat Tinggi',
            'inovasi'          => 'Tinggi',
            'komentar'         => 'Mantap',
            'pdfFile'          => UploadedFile::fake()->create('kinerja.pdf', 100, 'application/pdf'),
        ];

        $resp = $this->post(route('EvaluasiMitraKinerja.store'), $payload);
        $resp->assertRedirect();

        $this->assertDatabaseHas('evaluasimitrakinerja', [
            'rekap_id'      => $rekap->id,
            'nodok'         => $rekap->no_dokumen,
            'mitra'         => $rekap->mitra_kerja_sama,
            'pengisi_mitra' => 'Ibu Penilai',
            'integritas'    => 4,
            'komunikasi'    => 5,
            'laporan'       => 5,
        ]);

        $row = EvaluasiMitraKinerja::first();
        $this->assertNotNull($row->file_pdf);
        Storage::disk('public')->assertExists($row->file_pdf);

        $rekap->refresh();
        $this->assertTrue((bool) $rekap->is_kinerja);
    }

    /** ---------- Index ---------- */

    /** @test */
    public function index_menampilkan_daftar()
    {
        // INDEX berada di grup auth
        $this->actingAs(User::factory()->create());

        $rekap = $this->makeRekap();
        EvaluasiMitraKinerja::create([
            'rekap_id'         => $rekap->id,
            'nodok'            => $rekap->no_dokumen,
            'mitra'            => $rekap->mitra_kerja_sama,
            'pengisi_mitra'    => 'Pak A',
            'integritas'       => 4,
            'keahlian'         => 3,
            'komunikasi'       => 5,
            'kerjasamatim'     => 4,
            'pengembangandiri' => 3,
            'kreativitas'      => 2,
            'bahasaasing'      => 1,
            'teknologi'        => 4,
            'manajerial'       => 3,
            'analisis'         => 4,
            'laporan'          => 5,
            'inovasi'          => 4,
        ]);

        $resp = $this->get(route('EvaluasiMitraKinerja.index'));
        $resp->assertStatus(200)->assertViewIs('evaluasikerjasamakinerja');
    }

    /** ---------- Update ---------- */

    /** @test */
    public function update_memetakan_nilai_dan_ganti_pdf()
    {
        // UPDATE berada di grup auth
        $this->actingAs(User::factory()->create());

        $rekap = $this->makeRekap();

        $ev = EvaluasiMitraKinerja::create([
            'rekap_id'         => $rekap->id,
            'nodok'            => $rekap->no_dokumen,
            'mitra'            => $rekap->mitra_kerja_sama,
            'pengisi_mitra'    => 'Pak A',
            'integritas'       => 3,
            'keahlian'         => 3,
            'komunikasi'       => 3,
            'kerjasamatim'     => 3,
            'pengembangandiri' => 3,
            'kreativitas'      => 3,
            'bahasaasing'      => 3,
            'teknologi'        => 3,
            'manajerial'       => 3,
            'analisis'         => 3,
            'laporan'          => 3,
            'inovasi'          => 3,
            'file_pdf'         => null,
        ]);

        $resp = $this->put(route('EvaluasiMitraKinerja.update', ['id' => $ev->idkinerja]), [
            'integritas'       => 'Tinggi',
            'keahlian'         => 'Cukup',
            'komunikasi'       => 'Sangat Tinggi',
            'kerjasamatim'     => 'Tinggi',
            'pengembangandiri' => 'Cukup',
            'kreativitas'      => 'Kurang',
            'bahasaasing'      => 'Sangat Kurang',
            'teknologi'        => 'Tinggi',
            'manajerial'       => 'Cukup',
            'analisis'         => 'Tinggi',
            'laporan'          => 'Sangat Tinggi',
            'inovasi'          => 'Tinggi',
            'pdfFile'          => UploadedFile::fake()->create('baru.pdf', 120, 'application/pdf'),
        ]);

        $resp->assertRedirect(route('EvaluasiMitraKinerja.index'));

        $ev->refresh();
        $this->assertSame(4, $ev->integritas);
        $this->assertSame(5, $ev->komunikasi);
        $this->assertNotNull($ev->file_pdf);
        Storage::disk('public')->assertExists($ev->file_pdf);
    }

    /** ---------- Delete ---------- */

    /** @test */
    public function delete_menghapus_record_file_dan_set_is_kinerja_false_jika_terakhir()
    {
        // DELETE berada di grup auth
        $this->actingAs(User::factory()->create());

        $rekap = $this->makeRekap(['is_kinerja' => true]);

        $ev = EvaluasiMitraKinerja::create([
            'rekap_id'         => $rekap->id,
            'nodok'            => $rekap->no_dokumen,
            'mitra'            => $rekap->mitra_kerja_sama,
            'pengisi_mitra'    => 'Pak B',
            'integritas'       => 4,
            'keahlian'         => 3,
            'komunikasi'       => 5,
            'kerjasamatim'     => 4,
            'pengembangandiri' => 3,
            'kreativitas'      => 2,
            'bahasaasing'      => 1,
            'teknologi'        => 4,
            'manajerial'       => 3,
            'analisis'         => 4,
            'laporan'          => 5,
            'inovasi'          => 4,
            'file_pdf'         => 'evaluasi_pdf/haha.pdf',
        ]);

        Storage::disk('public')->put('evaluasi_pdf/haha.pdf', 'PDF');

        $resp = $this->delete(route('EvaluasiMitraKinerja.delete', ['id' => $ev->idkinerja]));
        $resp->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseMissing('evaluasimitrakinerja', ['idkinerja' => $ev->idkinerja]);
        Storage::disk('public')->assertMissing('evaluasi_pdf/haha.pdf');

        $rekap->refresh();
        $this->assertFalse((bool) $rekap->is_kinerja);
    }

    /** ---------- Kirim link + OTP (perlu user login) ---------- */

    /** @test */
    public function kirim_link_dan_otp_menghasilkan_record_otp_dan_kirim_email()
    {
        // Walau route ini publik di web.php, test ini tetap login sesuai skenario dashboard admin
        $user = User::factory()->create(['email' => 'staff@example.com']);
        $this->actingAs($user);

        $rekap = $this->makeRekap(['email_pihak_mitra' => 'mitra@example.com']);

        $resp = $this->post(route('EvaluasiMitraKinerja.kirim', ['rekapId' => $rekap->id]));
        $resp->assertRedirect();

        $this->assertDatabaseHas('evaluasi_kinerja_otps', [
            'rekap_id'      => $rekap->id,
            'sent_to_email' => 'staff@example.com',
        ]);

        Mail::assertSent(\App\Mail\MitraEvaluasiLinkMail::class, fn($m) => $m->hasTo('mitra@example.com'));
        Mail::assertSent(\App\Mail\AdminOtpMail::class, fn($m) => $m->hasTo('staff@example.com'));
    }
}
