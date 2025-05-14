<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PelaksanaanKerjaSamaController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\RekapKerjaSamaController;
use App\Http\Controllers\EvaluasiMitraKinerjaController;
use App\Http\Controllers\EvaluasiMitraController;
use App\Http\Controllers\DashboardController;


// Public Routes
Route::get('/', function () {
    return view('login');
});

Route::get('/inputlaporanpelaksanaankerjasama', function () {
    return view('inputlaporanpelaksanaankerjasama');
});

Route::get('/laporanpelaksanaankerjasama', function () {
    return view('laporanpelaksanaankerjasama');
});

Route::get('/inputevaluasikerjasamakinerja', function () {
    return view('inputevaluasikerjasamakinerja');
});

Route::get('/evaluasikerjasamakinerja', [EvaluasiMitraKinerjaController::class, 'index'])->name('evaluasikerjasamakinerja.index');
Route::get('/evaluasikerjasamamitra', [EvaluasiMitraController::class, 'index'])->name('evaluasikerjasamamitra.index');



Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'loginPost'])->name('login.post');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $rekap = App\Models\RekapKerjaSama::with('laporanPelaksanaan')->paginate(10);
        return view('dashboard', compact('rekap'));
        })->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::get('/mitraaktif', function () {return view('mitraaktif');});

    Route::get('/mitrapasif', function () {return view('mitrapasif');});


    // Rekap Kerja Sama
    Route::prefix('rekap_kerja_sama')->group(function () {
        Route::get('/', [RekapKerjaSamaController::class, 'index'])->name('input_kerja_sama');
        Route::post('/', [RekapKerjaSamaController::class, 'store'])->name('store_kerja_sama');
        Route::match(['post', 'get'], '/data', [RekapKerjaSamaController::class, 'data'])->name('data_kerja_sama');
        Route::delete('/{id}', [RekapKerjaSamaController::class, 'delete'])->name('delete_kerja_sama');
        Route::get('/create', [RekapKerjaSamaController::class, 'create'])->name('input_kerja_sama');
        Route::get('/{id}/edit', [RekapKerjaSamaController::class, 'edit'])->name('rekap_kerja_sama.edit');
        Route::put('/{id}', [RekapKerjaSamaController::class, 'update'])->name('rekap_kerja_sama.update');
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

    // SuperAdmin Routes
    Route::prefix('superadmin')->group(function () {
        Route::get('/', [SuperAdminController::class, 'index'])->name('superadmin');
        Route::get('/create_user', [SuperAdminController::class, 'createUserForm'])->name('superadmin.create_user_form');
        Route::post('/store_user', [SuperAdminController::class, 'storeUser'])->name('superadmin.store_user');
        Route::post('/change_password', [SuperAdminController::class, 'changePassword'])->name('superadmin.change_password');
        Route::delete('/users/{user}', [SuperAdminController::class, 'deleteUser'])->name('superadmin.delete_user');
    });

    //Evaluasi Kepuasan Kinerja
    Route::prefix('EvaluasiMitraKinerja')->controller(EvaluasiMitraKinerjaController::class)->group(function () {
        Route::get('/', 'index')->name('EvaluasiMitraKinerja.form');
        Route::post('/', 'store')->name('EvaluasiMitraKinerja.store');
        Route::get('/create/{id}', 'create')->name('EvaluasiMitraKinerja.create');
    });

    Route::prefix('EvaluasiMitra')->controller(EvaluasiMitraController::class)->group(function () {
        Route::get('/', 'index')->name('EvaluasiMitra.form');
        Route::post('/', 'store')->name('EvaluasiMitra.store');
        Route::get('/create/{id}', 'create')->name('EvaluasiMitra.create');
    });

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
