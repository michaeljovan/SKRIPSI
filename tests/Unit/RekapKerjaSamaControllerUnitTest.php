<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\RekapKerjaSama;
use App\Models\User;

class RekapKerjaSamaControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    /** ====== Helpers ====== */

    protected function disableOnlyAuthRoleMiddleware(): void
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\cekrole::class,
            \App\Http\Middleware\PreventBackHistory::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);
    }

    protected function makeRekap(array $overrides = []): RekapKerjaSama
    {
        $base = [
            'no_dokumen'          => 'DOC-' . Str::upper(Str::random(5)),
            'unit'                => 'FTI',
            'mitra_kerja_sama'    => 'PT Contoh',
            'judul_kerja_sama'    => 'Judul Kegiatan',
            'bentuk_kerja_sama'   => 'Pendidikan, Penelitian',
            'jenis_kerja_sama'    => 'MoU',
            'pihak_ukdw'          => 'FTI UKDW',
            'pihak_mitra'         => 'PT Contoh',
            'email_pihak_mitra'   => 'mitra@example.com',
            'tanggal_mulai'       => '2025-08-20',
            'tanggal_selesai'     => '2025-09-02',
            'masa_berlaku'        => 14,
            'kategori'            => 'nasional',
            'in_kind'             => null,
            'in_cash'             => null,
            'total_in_kind'       => null,
            'total_in_cash'       => null,
            'jumlah_implementasi' => null,
            'dokumen_path'        => 'dokumen_kerja_sama/sample.pdf',
            'status'              => 'aktif',
            'parent_id'           => null,
        ];

        return RekapKerjaSama::create(array_merge($base, $overrides));
    }

    /** ====== Bootstrapping per test ====== */

    protected function setUp(): void
    {
        parent::setUp();

        // Freeze time → 2025-08-27 untuk uji stop()
        Carbon::setTestNow(Carbon::parse('2025-08-27 12:00:00', 'Asia/Jakarta'));

        // Fake public storage
        Storage::fake('public');

        // Matikan auth/role/CSRF supaya tidak 401/302
        $this->disableOnlyAuthRoleMiddleware();

        // Route dummy jika belum ada (biar test jalan di semua web.php)
        if (!app('router')->has('evaluasi.kinerja.otp.show')) {
            Route::get('/__dummy-otp/{rekap}', fn () => 'ok')->name('evaluasi.kinerja.otp.show');
        }
        if (!app('router')->has('rekapkerjasama.options')) {
            Route::get('/rekapkerjasama/options', [\App\Http\Controllers\RekapKerjaSamaController::class, 'options'])
                ->name('rekapkerjasama.options');
        }
        if (!app('router')->has('rekap.sendEvaluasiOtp')) {
            Route::post('/rekapkerjasama/{rekap}/send-evaluasi-otp', [\App\Http\Controllers\RekapKerjaSamaController::class, 'sendEvaluasiOtp'])
                ->name('rekap.sendEvaluasiOtp');
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }


    /** ====== lihatPDF ====== */

    /** @test */
    public function lihat_pdf_mengembalikan_file_pdf_200()
    {
        $rekap = $this->makeRekap(['dokumen_path' => 'dokumen_kerja_sama/lihat.pdf']);
        Storage::disk('public')->put('dokumen_kerja_sama/lihat.pdf', 'pdf');

        $this->get(route('rekapkerjasama.pdf', ['id' => $rekap->id]))
             ->assertStatus(200);
    }

    /** @test */
    public function lihat_pdf_404_jika_file_tidak_ada()
    {
        $rekap = $this->makeRekap(['dokumen_path' => 'dokumen_kerja_sama/tidak-ada.pdf']);
        $this->get(route('rekapkerjasama.pdf', ['id' => $rekap->id]))
             ->assertStatus(404);
    }

    /** ====== stopForm & stop ====== */

    /** @test */
    public function stop_form_menampilkan_view_dengan_preview_durasi()
    {
        $rekap = $this->makeRekap([
            'tanggal_mulai'   => '2025-08-20',
            'tanggal_selesai' => '2025-09-02',
            'status'          => 'aktif',
        ]);

        $this->get(route('rekapkerjasama.stop.form', ['id' => $rekap->id]))
             ->assertStatus(200)
             ->assertViewIs('stopkerjasama')
             ->assertViewHasAll(['rekap','today','isSelesai','newDurasi']);
    }

    /** @test */
    public function stop_menghentikan_kerja_sama_aktif_mengubah_status_dan_tanggal()
    {
        $rekap = $this->makeRekap([
            'tanggal_mulai'   => '2025-08-20',
            'tanggal_selesai' => '2025-09-02',
            'status'          => 'aktif',
        ]);

        $this->patch(route('rekapkerjasama.stop', ['id' => $rekap->id]), [
                'alasan' => 'Dihentikan oleh admin'
            ])
            ->assertRedirect(route('data_kerja_sama'))
            ->assertSessionHas('success');

        $rekap->refresh();
        $this->assertSame('dihentikan', $rekap->status);

        // tanggal_selesai bisa Carbon → normalisasi dulu
        $tanggal = $rekap->tanggal_selesai instanceof \Carbon\Carbon
            ? $rekap->tanggal_selesai->toDateString()
            : $rekap->tanggal_selesai;

        $this->assertSame('2025-08-27', $tanggal); // testNow
        $this->assertSame(8, $rekap->masa_berlaku); // 20..27 inklusif
        $this->assertNotNull($rekap->stopped_at);
        $this->assertSame('Dihentikan oleh admin', $rekap->stopped_reason);
    }

    /** @test */
    public function stop_mengembalikan_info_jika_sudah_selesai()
    {
        $rekap = $this->makeRekap([
            'tanggal_mulai'   => '2025-08-10',
            'tanggal_selesai' => '2025-08-15', // < testNow → selesai
            'status'          => 'selesai',
        ]);

        $this->patch(route('rekapkerjasama.stop', ['id' => $rekap->id]), [
                'alasan' => 'apapun'
            ])
            ->assertRedirect(route('data_kerja_sama'))
            ->assertSessionHas('info');
    }

    /** @test */
    public function stop_mengembalikan_info_jika_sudah_dihentikan()
    {
        $rekap = $this->makeRekap([
            'status'          => 'dihentikan',
            'tanggal_mulai'   => '2025-08-10',
            'tanggal_selesai' => '2025-08-20',
        ]);

        $this->patch(route('rekapkerjasama.stop', ['id' => $rekap->id]), [
                'alasan' => 'apapun'
            ])
            ->assertRedirect(route('data_kerja_sama'))
            ->assertSessionHas('info');
    }

    /** ====== delete ====== */


    
}
