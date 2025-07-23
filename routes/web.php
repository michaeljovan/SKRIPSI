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


// Public Routes
Route::get('/', function () {
    return view('login');
});

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'loginPost'])->name('login.post');


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
