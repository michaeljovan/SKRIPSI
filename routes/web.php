<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PelaksanaanKerjaSamaController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\RekapKerjaSamaController;

// Public Routes
Route::get('/', function () {
    return view('login');
});

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'loginPost'])->name('login.post');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $rekap = App\Models\RekapKerjaSama::with('laporanPelaksanaan')->paginate(10);
        return view('dashboard', compact('rekap'));
    })->name('dashboard');

    // Rekap Kerja Sama
    Route::prefix('rekap_kerja_sama')->group(function () {
        Route::get('/', [RekapKerjaSamaController::class, 'index'])->name('input_kerja_sama');
        Route::post('/', [RekapKerjaSamaController::class, 'store'])->name('store_kerja_sama');
        Route::get('/data', [RekapKerjaSamaController::class, 'data'])->name('data_kerja_sama');
        Route::delete('/{id}', [RekapKerjaSamaController::class, 'delete'])->name('delete_kerja_sama');
    });

    // Pelaksanaan Kerja Sama
    Route::prefix('pelaksanaan')->group(function () {
        Route::get('/', [PelaksanaanKerjaSamaController::class, 'index'])->name('pelaksanaan.index');
        Route::get('/create/{id}', [PelaksanaanKerjaSamaController::class, 'create'])->name('pelaksanaan.create');
        Route::post('/', [PelaksanaanKerjaSamaController::class, 'store'])->name('pelaksanaan.store');
        Route::get('/{id}', [PelaksanaanKerjaSamaController::class, 'show'])->name('pelaksanaan.show');
        Route::get('/edit/{id}', [PelaksanaanKerjaSamaController::class, 'edit'])->name('pelaksanaan.edit');
        Route::delete('/{id}', [PelaksanaanKerjaSamaController::class, 'destroy'])->name('pelaksanaan.destroy');
    });

    // Dokumen Kerja Sama
    Route::get('/datadokumenkerjasama', [RekapKerjaSamaController::class, 'data'])->name('data_kerja_sama');

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

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
