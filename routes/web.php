<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PelaksanaanKerjaSamaController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\RekapKerjaSamaController;
use App\Http\Controllers\EvaluasiMitraKinerjaController;
use App\Http\Controllers\EvaluasiMitraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MitraAktifController;
use App\Http\Controllers\MitraPasifController;
use App\Http\Controllers\EvaluationGateController;
use App\Http\Controllers\EvaluasiLinkController;
use App\Http\Controllers\EvaluasiMitraKinerjaPeroranganController;
use App\Http\Controllers\EvaluasiMitraPeroranganController;

// Public Routes
Route::get('/', function () {
    return view('login');
});


Route::get('/evaluasi-kinerja/{rekapId}/pilihan', [EvaluasiMitraKinerjaController::class, 'pilihanForm'])
    ->name('EvaluasiMitraKinerja.pilihan');

Route::get('/evaluasi-kinerja/{id}/create', [EvaluasiMitraKinerjaController::class, 'create'])
    ->name('EvaluasiMitraKinerja.create');

Route::get('/evaluasi/link/{rekap}/{token}', [EvaluasiLinkController::class, 'show'])
    ->name('EvaluasiLink.show');

Route::get('/evaluasi/link/start/{mode}', [EvaluasiLinkController::class, 'start'])
    ->name('EvaluasiLink.start');

Route::get('evaluasi-mitra/perorangan/{id}', [EvaluasiMitraPeroranganController::class, 'create'])
    ->name('EvaluasiMitraPerorangan.create');

Route::post('evaluasi-mitra/perorangan/{id}', [EvaluasiMitraPeroranganController::class, 'store'])
    ->name('EvaluasiMitraPerorangan.store');

Route::post('/rekap/{rekap}/send-evaluasi-link', [RekapKerjaSamaController::class, 'sendEvaluasiLink'])
    ->name('evaluasi.link.send');

Route::get('/evaluasi/kinerja/{rekap}/form', [EvaluasiMitraKinerjaController::class, 'pilihanForm'])
    ->name('evaluasi.kinerja.form');

Route::get('evaluasi-mitra/{rekapId}/pilihan', [EvaluasiMitraController::class, 'pilihanForm'])
    ->name('EvaluasiMitra.pilihan');


Route::get(
    'evaluasi-mitra-kinerja/perorangan/{id}/create',
    [EvaluasiMitraKinerjaPeroranganController::class, 'create']
)->name('EvaluasiMitraKinerjaPerorangan.create');

Route::middleware(['auth']) // atau sesuai kebutuhanmu
    ->get('/api/rekap/detail', [RekapKerjaSamaController::class, 'apiDetail'])
    ->name('api.rekap.detail');


Route::post(
    'evaluasi-mitra-kinerja/perorangan/{id}',
    [EvaluasiMitraKinerjaPeroranganController::class, 'store']
)->name('EvaluasiMitraKinerjaPerorangan.store');

// Options JSON (dropdown induk) — beri nama:
Route::get('/rekapkerjasama/options', [\App\Http\Controllers\RekapKerjaSamaController::class, 'options'])
    ->name('rekapkerjasama.options');

Route::post('evaluasi-mitra/{rekapId}/kirim', [EvaluasiMitraController::class, 'kirimLink'])
    ->name('EvaluasiMitra.kirim');

// Kirim OTP Evaluasi Kinerja (staff):
Route::post('/rekapkerjasama/{rekap}/send-evaluasi-otp', [\App\Http\Controllers\RekapKerjaSamaController::class, 'sendEvaluasiOtp'])
    ->name('rekap.sendEvaluasiOtp');

// (sudah ada di kodenya) Gate OTP yang dipakai dalam email:
Route::get('/__dummy-otp/{rekap}', fn() => 'ok')->name('evaluasi.kinerja.otp.show'); // hapus ini jika kamu sudah punya route aslinya

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'loginPost'])->name('login.post');

Route::get('/rekapkerjasama/{id}/pdf', [RekapKerjaSamaController::class, 'lihatPDF'])->name('rekapkerjasama.pdf');

Route::post('/evaluasi-mitra/{rekap}/send-otp', [EvaluasiMitraController::class, 'kirimLinkDanOtp'])
    ->name('evaluasi.mitra.send_otp');

Route::get('/evaluasi-mitra/{rekapId}/otp', [EvaluasiMitraController::class, 'showOtpGate'])
    ->name('EvaluasiMitra.otpGate');

Route::post('/evaluasi-mitra/{rekap}/otp-verify', [EvaluasiMitraController::class, 'verifyOtp'])
    ->name('evaluasi.mitra.otp.verify');

// create form setelah OTP valid
Route::get('/evaluasi-mitra/create/{id}', [EvaluasiMitraController::class, 'create'])
    ->name('EvaluasiMitra.create');

Route::patch('/rekap-kerja-sama/{id}/stop', [RekapKerjaSamaController::class, 'stop'])
    ->name('rekapkerjasama.stop');

Route::get('rekapkerjasama/{id}/stop', [RekapKerjaSamaController::class, 'stopForm'])
    ->name('rekapkerjasama.stop.form');

// EKSEKUSI stop (PATCH)
Route::patch('rekapkerjasama/{id}/stop', [RekapKerjaSamaController::class, 'stop'])
    ->name('rekapkerjasama.stop');

// Gerbang OTP (GET form)
Route::get(
    '/evaluasi-mitra-kinerja/{rekapId}/otp',
    [EvaluasiMitraKinerjaController::class, 'showOtpGate']
)->name('EvaluasiMitraKinerja.otpGate');

// Verifikasi OTP (POST)
Route::post(
    '/evaluasi-mitra-kinerja/{rekapId}/otp',
    [EvaluasiMitraKinerjaController::class, 'verifyOtp']
)->name('EvaluasiMitraKinerja.verifyOtp');

// Kirim link ke mitra + OTP ke admin (POST dari dashboard admin)
Route::post(
    '/evaluasi-mitra-kinerja/{rekapId}/kirim',
    [EvaluasiMitraKinerjaController::class, 'kirimLinkDanOtp']
)->name('EvaluasiMitraKinerja.kirim');

// Akses form setelah OTP valid (sudah kamu pakai di verifyOtp)
Route::get(
    '/evaluasi-mitra-kinerja/{id}/create',
    [EvaluasiMitraKinerjaController::class, 'create']
)->name('EvaluasiMitraKinerja.create');

Route::get(
    'evaluasi-mitra-kinerja/{id}/create-direct',
    [EvaluasiMitraKinerjaController::class, 'create']
)->name('EvaluasiMitraKinerja.create_direct');

Route::get('/kerjasamaberakhir', [RekapKerjaSamaController::class, 'kerjasamaBerakhir'])
    ->name('kerjasamaberakhir');

Route::get('/rekapkerjasama/options', [RekapKerjaSamaController::class, 'options']);

// Autentikasi Routes
Route::middleware(['auth', 'cekrole:dekanat,staff'])->group(function () {

    Route::get('/chart-kategori-per-unit', [RekapKerjaSamaController::class, 'chartKategoriPerUnit']);
    Route::get('/dashboard/filter-kategori', [DashboardController::class, 'filterKategori']);
    Route::get('/api/dokumen-induk', [RekapKerjaSamaController::class, 'getDokumenInduk'])->name('api.dokumen_induk');
    Route::get('/cek-nodokumen', [RekapKerjaSamaController::class, 'cekNoDokumen'])->name('cek.no_dokumen');

    // Dashboard
    Route::get('/dashboard', function () {
        $rekap = App\Models\RekapKerjaSama::with('laporanPelaksanaan')->paginate(10);
        return view('dashboard', compact('rekap'));
    })->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });

    //Dashboard filter
    Route::get('/dashboard/filter', [DashboardController::class, 'filterByYear']);

    // Mitra aktif dashboard dan detailnya
    Route::get('/mitraaktif', [MitraAktifController::class, 'index'])->name('mitraaktifindex');


    Route::get('/mitrapasif', [MitraPasifController::class, 'index'])->name('mitrapasifindex');


    // Rekap Kerja Sama
    Route::prefix('rekapkerjasama')->name('rekapkerjasama.')->group(function () {
        Route::get('/', [RekapKerjaSamaController::class, 'index'])->name('index');
        Route::get('/create', [RekapKerjaSamaController::class, 'create'])->name('create');
        Route::post('/', [RekapKerjaSamaController::class, 'store'])->name('store');
        Route::get('/data', [RekapKerjaSamaController::class, 'data'])->name('data');
        Route::get('/{id}/edit', [RekapKerjaSamaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RekapKerjaSamaController::class, 'update'])->name('update');
        Route::delete('/{id}', [RekapKerjaSamaController::class, 'delete'])->name('delete');
    });

    // Pelaksanaan Kerja Sama
    Route::prefix('pelaksanaankerjasama')->group(function () {
        Route::get('/', [PelaksanaanKerjaSamaController::class, 'index'])->name('pelaksanaankerjasama.index');
        Route::get('/create/{id}', [PelaksanaanKerjaSamaController::class, 'create'])->name('pelaksanaankerjasama.create');
        Route::post('/', [PelaksanaanKerjaSamaController::class, 'store'])->name('pelaksanaankerjasama.store');
        Route::get('/{id}', [PelaksanaanKerjaSamaController::class, 'show'])->name('pelaksanaankerjasama.show');
        Route::delete('/{id}', [PelaksanaanKerjaSamaController::class, 'destroy'])->name('pelaksanaankerjasama.destroy');
        Route::put('/{id}', [PelaksanaanKerjaSamaController::class, 'update'])->name('pelaksanaankerjasama.update');
        Route::get('/edit/{id}', [PelaksanaanKerjaSamaController::class, 'edit'])->name('pelaksanaankerjasama.edit');
    });

    // Dokumen Kerja Sama
    Route::match(['get', 'post'], '/datadokumenkerjasama', [RekapKerjaSamaController::class, 'index'])->name('data_kerja_sama');

    // Laporan Pelaksanaan
    Route::get('/laporanpelaksanaankerjasama', [PelaksanaanKerjaSamaController::class, 'index'])->name('laporan.pelaksanaan');

    //Evaluasi Kepuasan Kinerja
    Route::prefix('EvaluasiMitraKinerja')->controller(EvaluasiMitraKinerjaController::class)->group(function () {
        Route::get('/', 'index')->name('EvaluasiMitraKinerja.index');
        Route::post('/', 'store')->name('EvaluasiMitraKinerja.store');
        Route::get('/create/{id}', 'create')->name('EvaluasiMitraKinerja.create');
        Route::delete('/{id}', 'delete')->name('EvaluasiMitraKinerja.delete');
        Route::get('/{id}/edit', 'edit')->name('EvaluasiMitraKinerja.edit');
        Route::put('/{id}', 'update')->name('EvaluasiMitraKinerja.update');
    });

    Route::prefix('EvaluasiMitra')->controller(EvaluasiMitraController::class)->group(function () {
        Route::get('/', 'index')->name('EvaluasiMitra.index');
        Route::post('/', 'store')->name('EvaluasiMitra.store');
        Route::get('/create/{id}', 'create')->name('EvaluasiMitra.create');
        Route::delete('/{id}', 'delete')->name('EvaluasiMitra.delete');
        Route::get('/{id}/edit', 'edit')->name('EvaluasiMitra.edit');
        Route::put('/{id}', 'update')->name('EvaluasiMitra.update');
    });
    // Logout
});

Route::middleware(['auth', 'cekrole:dekanat'])->group(function () {
    Route::get('/chart-kategori-per-unit', [RekapKerjaSamaController::class, 'chartKategoriPerUnit']);
    Route::get('/dashboard/filter-kategori', [DashboardController::class, 'filterKategori']);
    Route::get('/api/dokumen-induk', [RekapKerjaSamaController::class, 'getDokumenInduk'])->name('api.dokumen_induk');
    Route::get('/cek-nodokumen', [RekapKerjaSamaController::class, 'cekNoDokumen'])->name('cek.no_dokumen');

    Route::prefix('superadmin')->group(function () {
        Route::get('/', [SuperAdminController::class, 'index'])->name('superadmin');
        Route::get('/create_user', [SuperAdminController::class, 'createUserForm'])->name('superadmin.create_user_form');
        Route::post('/store_user', [SuperAdminController::class, 'storeUser'])->name('superadmin.store_user');
        Route::post('/change_password', [SuperAdminController::class, 'changePassword'])->name('superadmin.change_password');
        Route::delete('/users/{user}', [SuperAdminController::class, 'deleteUser'])->name('superadmin.delete_user');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/api/dokumen-induk', [RekapKerjaSamaController::class, 'getDokumenInduk'])->name('api.dokumen_induk');
    Route::get('/chart-kategori-per-unit', [RekapKerjaSamaController::class, 'chartKategoriPerUnit']);
    Route::get('/dashboard/filter-kategori', [DashboardController::class, 'filterKategori']);
    Route::get('/cek-nodokumen', [RekapKerjaSamaController::class, 'cekNoDokumen'])->name('cek.no_dokumen');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
