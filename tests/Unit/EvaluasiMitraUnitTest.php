<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitra;
use App\Models\EvaluasiKinerjaOtp;

use App\Http\Middleware\cekrole;
use App\Http\Middleware\PreventBackHistory;

class EvaluasiMitraUnitTest extends TestCase
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

    /** ========== OTP ========== */

    /** @test */
    public function verify_otp_sukses_set_session_dan_redirect_ke_create()
    {
        $rekap = $this->makeRekap();

        $plain = '123456';
        EvaluasiKinerjaOtp::create([
            'rekap_id'      => $rekap->id,
            'code_hash'     => Hash::make($plain),
            'expires_at'    => now()->addMinutes(30),
            'used_at'       => null,
            'sent_to_email' => 'admin@example.com',
        ]);

        $resp = $this->post(route('evaluasi.mitra.otp.verify', ['rekap' => $rekap->id]), ['otp' => $plain]);

        $resp->assertRedirect(route('EvaluasiMitra.create', ['id' => $rekap->id]));
        $this->assertEquals($rekap->id, (int) session('evaluasi_mitra_allowed'));
    }

    /** @test */
    public function verify_otp_gagal_kode_salah()
    {
        $rekap = $this->makeRekap();

        EvaluasiKinerjaOtp::create([
            'rekap_id'      => $rekap->id,
            'code_hash'     => Hash::make('111111'),
            'expires_at'    => now()->addMinutes(30),
            'used_at'       => null,
            'sent_to_email' => 'admin@example.com',
        ]);

        $resp = $this->from(route('EvaluasiMitra.otpGate', ['rekapId' => $rekap->id]))
            ->post(route('evaluasi.mitra.otp.verify', ['rekap' => $rekap->id]), ['otp' => '222222']);

        $resp->assertRedirect(route('EvaluasiMitra.otpGate', ['rekapId' => $rekap->id]));
        $resp->assertSessionHasErrors('otp');
    }

    /** ========== Create View ========== */

    /** @test */
    public function create_menampilkan_form_evaluasi_mitra()
    {
        $rekap = $this->makeRekap();

        // JANGAN mematikan semua middleware: tetap pertahankan group 'web'
        $this->actingAs(User::factory()->create());
        $this->withoutMiddleware([cekrole::class, PreventBackHistory::class]);

        $resp = $this->get(route('EvaluasiMitra.create', ['id' => $rekap->id]));
        $resp->assertStatus(200);
        $resp->assertViewIs('inputevaluasikerjasamamitra');
        $resp->assertViewHasAll(['rekap', 'dosenCount', 'mahasiswaCount']);
    }

    /** ========== Store ========== */

    /** @test */
    public function store_menyimpan_mapping_nilai_upload_pdf_dan_set_is_mitra_true()
    {
        Storage::fake('public');
        $rekap = $this->makeRekap();

        $payload = [
            'rekap_id'         => $rekap->id,
            'nodok'            => $rekap->no_dokumen,
            'mitra'            => $rekap->mitra_kerja_sama,
            'pengisi_mitra'    => 'Ibu Mitra',
            'integritas'       => 'Tinggi',
            'keahlian'         => 'Cukup',
            'komunikasi'       => 'Sangat Tinggi',
            'kerjasamatim'     => 'Tinggi',
            'pengembangandiri' => 'Cukup',
            'kreativitas'      => 'Kurang',
            'bahasaasing'      => 'Sangat Kurang',
            'komentar'         => 'Mantap',
            'pdfFile'          => UploadedFile::fake()->create('kepuasan.pdf', 100, 'application/pdf'),
        ];

        $this->actingAs(User::factory()->create());
        $this->withoutMiddleware([cekrole::class, PreventBackHistory::class]);

        $resp = $this->post(route('EvaluasiMitra.store'), $payload);
        $resp->assertRedirect(); // back()->with('success', ...)

        $this->assertDatabaseHas('evaluasimitra', [
            'rekap_id'      => $rekap->id,
            'nodok'         => $rekap->no_dokumen,
            'mitra'         => $rekap->mitra_kerja_sama,
            'pengisi_mitra' => 'Ibu Mitra',
            'integritas'    => 4, // Tinggi
            'komunikasi'    => 5, // Sangat Tinggi
        ]);

        $row = EvaluasiMitra::first();
        $this->assertNotNull($row->file_pdf);
        Storage::disk('public')->assertExists($row->file_pdf);

        $rekap->refresh();
        $this->assertTrue((bool) $rekap->is_mitra);
    }

    /** ========== Delete ========== */

    /** @test */
    public function delete_menghapus_record_dan_menyetel_is_mitra_false()
    {
        $rekap = $this->makeRekap(['is_mitra' => true]);

        $ev = EvaluasiMitra::create([
            'rekap_id'         => $rekap->id,
            'nodok'            => $rekap->no_dokumen,
            'mitra'            => $rekap->mitra_kerja_sama,
            'pengisi_mitra'    => 'Pak Mitra',
            'integritas'       => 4,
            'keahlian'         => 3,
            'komunikasi'       => 5,
            'kerjasamatim'     => 4,
            'pengembangandiri' => 3,
            'kreativitas'      => 2,
            'bahasaasing'      => 1,
        ]);

        $this->actingAs(User::factory()->create());
        $this->withoutMiddleware([cekrole::class, PreventBackHistory::class]);

        $resp = $this->delete(route('EvaluasiMitra.delete', ['id' => $ev->idmitra]));
        $resp->assertStatus(200);

        $this->assertDatabaseMissing('evaluasimitra', ['idmitra' => $ev->idmitra]);

        $rekap->refresh();
        $this->assertFalse((bool) $rekap->is_mitra);
    }

    /** @test */
    public function delete_mengembalikan_400_untuk_id_tidak_valid()
    {
        $this->actingAs(User::factory()->create());
        $this->withoutMiddleware([cekrole::class, PreventBackHistory::class]);

        $resp = $this->delete(route('EvaluasiMitra.delete', ['id' => 'abc']));
        $resp->assertStatus(400);
    }

    /** ========== Update ========== */


    /** ========== Kirim Link + OTP ========== */

    /** @test */
    public function kirim_link_dan_otp_membuat_record_otp_dan_mengirim_email()
    {
        Mail::fake();
        config()->set('mail.admin_address', 'admin@example.com');

        $rekap = $this->makeRekap(['email_pihak_mitra' => 'mitra@example.com']);

        // endpoint publik — aman login/no-middleware
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
