<?php

use App\Http\Controllers\AbsenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect('/login');
    }

    return auth()->user()->role === 'admin' ? redirect('/admin') : redirect('/absen');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/absen', [AbsenController::class, 'index'])->name('absen');
    Route::post('/absen/masuk', [AbsenController::class, 'masuk'])->name('absen.masuk');
    Route::post('/absen/pulang', [AbsenController::class, 'pulang'])->name('absen.pulang');

    // Area admin: dijaga satpam AdminOnly, karyawan biasa kena 403
    Route::middleware(AdminOnly::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/rekap', [AdminController::class, 'rekap'])->name('rekap');
        Route::get('/rekap/csv', [AdminController::class, 'exportCsv'])->name('rekap.csv');
        Route::get('/kantor', [AdminController::class, 'kantor'])->name('kantor');
        Route::post('/kantor', [AdminController::class, 'simpanKantor'])->name('kantor.simpan');
    });
});