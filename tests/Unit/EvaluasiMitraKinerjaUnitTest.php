<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitraKinerja;
use App\Models\EvaluasiKinerjaOtp;

class EvaluasiMitraKinerjaUnitTest extends TestCase
{
    use RefreshDatabase;

    /** Helper: buat 1 rekap minimal valid */
    private function makeRekap(array $override = []): RekapKerjaSama
    {
        $base = [
            'no_dokumen'        => 'DOC-' . Str::upper(Str::random(5)),
            'unit'              => 'Informatika',
            'mitra_kerja_sama'  => 'PT Contoh',
            'judul_kerja_sama'  => 'Judul Kerma',
            'bentuk_kerja_sama' => 'Pendidikan',
            'jenis_kerja_sama'  => 'MoU',
            'pihak_ukdw'        => 'FTI',
            'pihak_mitra'       => 'PT Contoh',
            'email_pihak_mitra' => 'mitra@example.com',
            'tanggal_mulai'     => now()->subDays(1)->toDateString(),
            'tanggal_selesai'   => now()->addDays(5)->toDateString(),
            'masa_berlaku'      => 7,
            'kategori'          => 'nasional',
            'dokumen_path'      => 'dummy.pdf',
            'is_laporan'        => false,
            'is_kinerja'        => false,
            'is_mitra'          => false,
        ];

        return RekapKerjaSama::create(array_merge($base, $override));
    }

    /** @test */
    public function verify_otp_sukses_menyetel_session_dan_redirect_ke_create()
    {
        $rekap = $this->makeRekap();

        // Simpan OTP valid
        $plain = '123456';
        EvaluasiKinerjaOtp::create([
            'rekap_id'      => $rekap->id,
            'code_hash'     => Hash::make($plain),
            'expires_at'    => now()->addMinutes(30),
            'used_at'       => null,
            'sent_to_email' => 'admin@example.com',
        ]);

        $resp = $this->post(route('EvaluasiMitraKinerja.verifyOtp', ['rekapId' => $rekap->id]), [
            'otp' => $plain,
        ]);

        $resp->assertRedirect(route('EvaluasiMitraKinerja.create', ['id' => $rekap->id]));
        $this->assertEquals($rekap->id, (int) session('evaluasi_mitra_kinerja_allowed'));
    }

    /** @test */
    public function verify_otp_gagal_bila_kode_salah()
    {
        $rekap = $this->makeRekap();

        EvaluasiKinerjaOtp::create([
            'rekap_id'      => $rekap->id,
            'code_hash'     => Hash::make('111111'),
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

    /** @test */
    public function create_wajib_via_otp_gate_saat_session_tidak_sah()
    {
        $rekap = $this->makeRekap();

        // Penting: bypass middleware auth/role supaya tes fokus ke gate OTP
        $this->withoutMiddleware();

        $resp = $this->get(route('EvaluasiMitraKinerja.create', ['id' => $rekap->id]));
        $resp->assertRedirect(route('EvaluasiMitraKinerja.otpGate', ['rekapId' => $rekap->id]));
        $resp->assertSessionHas('error');
    }

    /** @test */
    public function store_menyimpan_evaluasi_memetakan_nilai_menandai_is_kinerja_true_dan_upload_pdf()
    {
        Storage::fake('public');
        $rekap = $this->makeRekap();

        $payload = [
            'rekap_id'         => $rekap->id,
            'nodok'            => $rekap->no_dokumen,
            'mitra'            => $rekap->mitra_kerja_sama,
            'pengisi_mitra'    => 'Bpk. Mitra',
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
            'komentar'         => 'Bagus',
            'pdfFile'          => UploadedFile::fake()->create('eval.pdf', 120, 'application/pdf'),
        ];

        // Bypass middleware & set session OTP valid
        $this->withoutMiddleware();
        $this->withSession(['evaluasi_mitra_kinerja_allowed' => $rekap->id]);

        $resp = $this->post(route('EvaluasiMitraKinerja.store'), $payload);
        $resp->assertRedirect(); // back()->with('success', ...)

        // Tersimpan di DB (cek sebagian field & mapping angka)
        $this->assertDatabaseHas('evaluasimitrakinerja', [
            'rekap_id'      => $rekap->id,
            'nodok'         => $rekap->no_dokumen,
            'mitra'         => $rekap->mitra_kerja_sama,
            'integritas'    => 4, // Tinggi
            'komunikasi'    => 5, // Sangat Tinggi
            'pengisi_mitra' => 'Bpk. Mitra',
        ]);

        $row = EvaluasiMitraKinerja::first();
        $this->assertNotNull($row->file_pdf);
        Storage::disk('public')->assertExists($row->file_pdf);

        $rekap->refresh();
        $this->assertTrue((bool) $rekap->is_kinerja);
    }

    /** @test */
    public function delete_menghapus_record_file_dan_menyetel_is_kinerja_false_jika_terakhir()
    {
        Storage::fake('public');

        $rekap = $this->makeRekap(['is_kinerja' => true]);

        $path = 'evaluasi_pdf/sample.pdf';
        Storage::disk('public')->put($path, 'dummy');
        $ev = EvaluasiMitraKinerja::create([
            'rekap_id'         => $rekap->id,
            'nodok'            => $rekap->no_dokumen,
            'mitra'            => $rekap->mitra_kerja_sama,
            'pengisi_mitra'    => 'Bpk. Mitra',
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
            'file_pdf'         => $path,
        ]);

        // Matikan middleware biar endpoint delete bisa diakses langsung
        $this->withoutMiddleware();

        $resp = $this->delete(route('EvaluasiMitraKinerja.delete', ['id' => $ev->idkinerja]));
        $resp->assertStatus(200);

        $this->assertDatabaseMissing('evaluasimitrakinerja', ['idkinerja' => $ev->idkinerja]);
        Storage::disk('public')->assertMissing($path);

        $rekap->refresh();
        $this->assertFalse((bool) $rekap->is_kinerja);
    }

    /** @test */
    public function kirim_link_dan_otp_membuat_record_otp_dan_mengirim_email()
    {
        Mail::fake();
        config()->set('mail.admin_address', 'admin@example.com');

        $rekap = $this->makeRekap(['email_pihak_mitra' => 'mitra@example.com']);

        $resp = $this->post(route('EvaluasiMitraKinerja.kirim', ['rekapId' => $rekap->id]));
        $resp->assertRedirect();

        // Ganti ke nama tabel sesuai migrasi OTP-mu
        $this->assertDatabaseHas('evaluasi_kinerja_otps', [
            'rekap_id'      => $rekap->id,
            'sent_to_email' => 'admin@example.com',
        ]);

        Mail::assertSent(\App\Mail\MitraEvaluasiLinkMail::class, fn($m) => $m->hasTo('mitra@example.com'));
        Mail::assertSent(\App\Mail\AdminOtpMail::class, fn($m) => $m->hasTo('admin@example.com'));
    }
}
