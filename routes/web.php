<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; 
use App\Http\Controllers\LoginController; 
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\RekapKerjaSamaController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});   

Route::get('/superadmin', function () {
    return view('superadmin');
});    

Route::get('/inputrekapkerjasama', function () {
    return view('inputrekapkerjasama');
});   

Route::get('/datadokumenkerjasama', function () {
    return view('datadokumenkerjasama');
});   



Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'loginPost'])->name('login.post');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', action: function () {
        return view('dashboard'); // Buat file dashboard.blade.php
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

//SuperAdmin Routes
Route::get('/superadmin', function () {return view('superadmin');})->name('superadmin');
// routes/web.php
Route::get('/superadmin/create-user', [SuperAdminController::class, 'createUserForm'])->name('superadmin.createUserForm');
Route::post('/superadmin/store-user', [SuperAdminController::class, 'storeUser'])->name('superadmin.storeUser');
Route::prefix('superadmin')->group(function() {
    Route::get('/', [SuperAdminController::class, 'createUserForm'])->name('superadmin');
    Route::post('/create-user', [SuperAdminController::class, 'storeUser'])->name('superadmin.storeUser');
});
//Route Ganti Password
Route::post('/superadmin/change-password', [SuperAdminController::class, 'changePassword'])->name('superadmin.changePassword');
//Route Hapus User
Route::delete('/superadmin/users/{user}', [SuperadminController::class, 'deleteUser'])->name('superadmin.deleteUser');


//ROUTE INPUT REKAP KERJA SAMA
Route::get('/input-kerja-sama', [RekapKerjaSamaController::class, 'index'])->name('input.kerja.sama');
Route::post('/input-kerja-sama', [RekapKerjaSamaController::class, 'store'])->name('store.kerja.sama');
