<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\RekapanController;
use App\Http\Controllers\BonusController;
use App\Http\Middleware\RoleCheck; // <-- Import Middleware yang baru kita buat

// ==========================================
// --- LALUAN GUEST (Belum Login) ---
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ==========================================
// --- LALUAN DILINDUNGI (Wajib Login) ---
// ==========================================
Route::middleware('auth')->group(function () {
    
    // ------------------------------------------
    // ZON BERSAMA (Admin & Karyawan)
    // ------------------------------------------
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/ganti-password', [PasswordController::class, 'edit'])->name('password.edit');

    Route::get('/rekapan', [RekapanController::class, 'index'])->name('rekapan');
    Route::get('/rekapan/detail/{id}', [RekapanController::class, 'detail'])->name('rekapan.detail');
    Route::post('/rekapan/detail/{id}', [RekapanController::class, 'updateDetail'])->name('rekapan.updateDetail');

    // ------------------------------------------
    // ZON PEKERJA (Kunci dengan RoleCheck:karyawan)
    // ------------------------------------------
    Route::middleware([RoleCheck::class.':karyawan'])->group(function () {
        Route::get('/', [AttendanceController::class, 'ceklok'])->name('ceklok');
        Route::post('/ceklok', [AttendanceController::class, 'store'])->name('ceklok.store');
    });

    // ------------------------------------------
    // ZON ADMIN (Kunci dengan RoleCheck:admin)
    // ------------------------------------------
    Route::middleware([RoleCheck::class.':admin'])->group(function () {
        Route::get('/bonus', [BonusController::class, 'index'])->name('bonus');

        Route::get('/akun', [AccountController::class, 'index'])->name('akun.index');
        Route::get('/akun/tambah', [AccountController::class, 'create'])->name('akun.create');
        Route::post('/akun', [AccountController::class, 'store'])->name('akun.store');
        Route::get('/akun/{id}/edit', [AccountController::class, 'edit'])->name('akun.edit');
        Route::put('/akun/{id}', [AccountController::class, 'update'])->name('akun.update');
        Route::delete('/akun/{id}', [AccountController::class, 'destroy'])->name('akun.destroy');

        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/tambah', [SettingController::class, 'create'])->name('setting.create');
        Route::post('/setting', [SettingController::class, 'store'])->name('setting.store');
        Route::get('/setting/{id}/edit', [SettingController::class, 'edit'])->name('setting.edit');
        Route::put('/setting/{id}', [SettingController::class, 'update'])->name('setting.update');
        
        Route::get('/setting/{office}/shift', [SettingController::class, 'shiftIndex'])->name('setting.shift');
        Route::post('/setting/{office}/shift', [SettingController::class, 'shiftStore'])->name('setting.shift.store');
        Route::delete('/setting/shift/{shift}', [SettingController::class, 'shiftDestroy'])->name('setting.shift.destroy');
    });
});