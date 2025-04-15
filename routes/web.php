<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\RekapKerjaSamaController;

Route::get('/', function () {return view('login');});
Route::get('/datadokumenkerjasama', function () {return view('datadokumenkerjasama');});
Route::get('/datadokumenkerjasama', [RekapKerjaSamaController::class, 'index'])->name('data_kerja_sama');


Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'loginPost'])->name('login.post');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
    //Input Rekap Kerja Sama
    Route::get('/inputrekapkerjasama', function () {return view('inputrekapkerjasama');})->name('inputrekapkerjasama');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // SuperAdmin Routes
    Route::prefix('superadmin')->group(function () {
        Route::get('/', [SuperAdminController::class, 'createUserForm'])->name('superadmin');
        Route::get('/create_user', [SuperAdminController::class, 'createUserForm'])->name('superadmin.create_user_form');
        Route::post('/store_user', [SuperAdminController::class, 'storeUser'])->name('superadmin.store_user');
        Route::post('/change_password', [SuperAdminController::class, 'changePassword'])->name('superadmin.change_password');
        Route::delete('/superadmin/users/{user}', [SuperAdminController::class, 'deleteUser'])->name('superadmin.delete_user');
    });
    // Rekap Kerja Sama Routes
    Route::prefix('rekap_kerja_sama')->group(function () {
        Route::get('/', [RekapKerjaSamaController::class, 'index'])->name('input_kerja_sama');
        Route::post('/', [RekapKerjaSamaController::class, 'store'])->name('store_kerja_sama');
        Route::get('/data', function () {
            return view('datadokumenkerjasama');
        })->name('data_kerja_sama');
    });
});
