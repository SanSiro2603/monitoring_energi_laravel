<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnergiController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('halamanawal');      // Halaman utama
})->name('home');

Route::get('/tentang', function () {
    return view('welcome');          // Halaman tentang
})->name('tentang');

// AUTH ROUTES
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// SUPER USER
Route::middleware(['auth', 'role:super_user'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin');
    });

    Route::get('/admin/laporan', [EnergiController::class, 'laporan']);
    Route::get('/laporan/admin/json', [EnergiController::class, 'laporanJson']);
    Route::get('/laporan/admin/export-pdf', [EnergiController::class, 'exportPdf']);
    Route::get('/laporan/admin/export-excel', [EnergiController::class, 'exportExcel']);

    Route::get('/admin/energi', [EnergiController::class, 'index']);
    Route::get('/admin/energi/create', [EnergiController::class, 'create']);
    Route::post('/admin/energi', [EnergiController::class, 'store']);
    Route::get('/admin/energi/{id}/edit', [EnergiController::class, 'edit']);
    Route::put('/admin/energi/{id}', [EnergiController::class, 'update']);
    Route::delete('/admin/energi/{id}', [EnergiController::class, 'destroy']);
    Route::post('/energi/import', [EnergiController::class, 'import']);
});

// DIVISI USER
Route::middleware(['auth', 'role:divisi_user'])->group(function () {
    Route::get('/divisi/dashboard', function () {
        return view('dashboard.divisi');
    });

    Route::get('/divisi/energi', [EnergiController::class, 'index']);
    Route::get('/divisi/energi/create', [EnergiController::class, 'create']);
    Route::post('/divisi/energi', [EnergiController::class, 'store']);
    Route::delete('/divisi/energi/{id}', [EnergiController::class, 'destroy']);
});

// USER UMUM
Route::middleware(['auth', 'role:user_umum'])->group(function () {
    Route::get('/umum/dashboard', function () {
        return view('dashboard.umum');
    });

    Route::get('/umum/summary', [EnergiController::class, 'summary']);
});

// ✅ PERBAIKAN: Route dengan nama 'dashboard'
Route::get('/dashboard', function () {
    $role = Auth::user()->role;

    if ($role === 'super_user') {
        return view('dashboard.admin');
    } elseif ($role === 'divisi_user') {
        return view('dashboard.divisi');
    } else {
        return view('dashboard.umum'); // fallback
    }
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil/upload', [ProfilController::class, 'uploadFoto'])->name('profil.upload');
});

// Lupa Password
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
