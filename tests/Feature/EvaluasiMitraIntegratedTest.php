<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitra;
use App\Models\EvaluasiKinerjaOtp;

class EvaluasiMitraIntegratedTest extends TestCase
{
    use RefreshDatabase;

    /** ---------- Helpers ---------- */

    protected function disableOnlyAuthRoleMiddleware(): void
    {
        // Matikan auth/role/CSRF, tapi tetap biarkan ShareErrorsFromSession
        $this->withoutMiddleware([
            \App\Http\Middleware\cekrole::class,
            \App\Http\Middleware\PreventBackHistory::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);
    }

    protected function makeRekap(array $override = []): RekapKerjaSama
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
            'tanggal_mulai'       => now()->subDays(1)->toDateString(),
            'tanggal_selesai'     => now()->addDays(7)->toDateString(),
            'masa_berlaku'        => 8,
            'kategori'            => 'nasional',
            'dokumen_path'        => 'dokumen_kerja_sama/sample.pdf',
            'is_laporan'          => false,
            'is_kinerja'          => false,
            'is_mitra'            => false,
            'status'              => 'aktif',
        ];

        return RekapKerjaSama::create(array_merge($base, $override));
    }

    /** ---------- Boot per test ---------- */

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Mail::fake();
        $this->disableOnlyAuthRoleMiddleware();
        $this->startSession(); // supaya $errors tersedia di Blade bila diperlukan
    }

    /** ---------- OTP Gate ---------- */

    /** @test */
    public function otp_gate_view_muncul()
    {
        $rekap = $this->makeRekap();
        $resp = $this->get(route('EvaluasiMitra.otpGate', ['rekapId' => $rekap->id]));
        $resp->assertStatus(200)->assertViewIs('evaluasi_mitra_otp_gate');
    }

    /** @test */
    public function verify_otp_sukses_set_session_dan_redirect_create()
    {
        $rekap = $this->makeRekap();

        // siapkan OTP valid
        EvaluasiKinerjaOtp::create([
            'rekap_id'      => $rekap->id,
            'code_hash'     => \Hash::make('123456'),
            'expires_at'    => now()->addMinutes(30),
            'used_at'       => null,
            'sent_to_email' => 'admin@example.com',
        ]);

        $resp = $this->post(route('evaluasi.mitra.otp.verify', ['rekap' => $rekap->id]), [
            'otp' => '123456',
        ]);

        $resp->assertRedirect(route('EvaluasiMitra.create', ['id' => $rekap->id]));
        $this->assertEquals($rekap->id, (int) session('evaluasi_mitra_allowed'));
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

        $resp = $this->from(route('EvaluasiMitra.otpGate', ['rekapId' => $rekap->id]))
            ->post(route('evaluasi.mitra.otp.verify', ['rekap' => $rekap->id]), [
                'otp' => '222222',
            ]);

        $resp->assertRedirect(route('EvaluasiMitra.otpGate', ['rekapId' => $rekap->id]));
        $resp->assertSessionHasErrors('otp');
    }


    /** ---------- Kirim Link + OTP ---------- */

    /** @test */
    public function kirim_link_dan_otp_menghasilkan_record_otp_dan_mengirim_email()
    {
        config()->set('mail.admin_address', 'admin@example.com');

        $rekap = $this->makeRekap(['email_pihak_mitra' => 'mitra@example.com']);

        // route ini publik di web.php kamu
        $resp = $this->post(route('evaluasi.mitra.send_otp', ['rekap' => $rekap->id]));
        $resp->assertRedirect();

        $this->assertDatabaseHas('evaluasi_kinerja_otps', [
            'rekap_id'      => $rekap->id,
            'sent_to_email' => 'admin@example.com',
        ]);

        Mail::assertSent(\App\Mail\MitraKepuasanLinkMail::class, fn($m) => $m->hasTo('mitra@example.com'));
        Mail::assertSent(\App\Mail\AdminOtpMail::class, fn($m) => $m->hasTo('admin@example.com'));
    }
}
