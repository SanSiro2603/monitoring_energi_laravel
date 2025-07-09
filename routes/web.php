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
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('halamanawal'); // Halaman utama
})->name('home');

Route::get('/tentang', function () {
    return view('welcome'); // Halaman tentang
})->name('tentang');

// AUTH ROUTES
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// SUPER USER
Route::middleware(['auth', 'verified', 'role:super_user', 'no_cache'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin');
    });

    Route::prefix('admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::get('/admin/laporan', [EnergiController::class, 'laporan']);
    Route::get('/laporan/admin/json', [EnergiController::class, 'laporanJson']);
    Route::get('/laporan/admin/export-pdf', [EnergiController::class, 'exportPdf']);
    Route::get('/laporan/admin/export-excel', [EnergiController::class, 'exportExcel']);
    Route::get('/export-energi', [EnergiController::class, 'exportExcel'])->name('export.energi');

    Route::get('/admin/energi', [EnergiController::class, 'index'])->name('admin.energi.index');
    Route::get('/admin/energi/create', [EnergiController::class, 'create']);
    Route::post('/admin/energi', [EnergiController::class, 'store']);
    Route::get('/admin/energi/{id}/edit', [EnergiController::class, 'edit']);
    Route::put('/admin/energi/{id}', [EnergiController::class, 'update']);
    Route::delete('/admin/energi/{id}', [EnergiController::class, 'destroy']);
    Route::post('/energi/import', [EnergiController::class, 'import']);
});

// DIVISI USER
Route::middleware(['auth', 'verified', 'role:divisi_user'])->group(function () {
    Route::get('/divisi/dashboard', function () {
        return view('dashboard.divisi');
    });

    Route::get('/divisi/energi', [EnergiController::class, 'index'])->name('divisi.energi.index');
    Route::get('/divisi/energi/create', [EnergiController::class, 'create']);
    Route::post('/divisi/energi', [EnergiController::class, 'store']);
    Route::delete('/divisi/energi/{id}', [EnergiController::class, 'destroy']);
});

Route::middleware(['auth', 'verified', 'role:divisi_user'])->prefix('divisi')->group(function () {
    Route::get('/users', [UserController::class, 'indexDivisi']);
    Route::get('/users/create', [UserController::class, 'createDivisi']);
    Route::post('/users', [UserController::class, 'storeDivisi']);
});

Route::middleware(['auth', 'verified'])->prefix('divisi')->group(function () {
    Route::get('/laporan', [EnergiController::class, 'laporan'])->name('divisi.laporan');
    Route::get('/laporan/export-excel', [EnergiController::class, 'exportExcel'])->name('divisi.export.excel');
    Route::get('/laporan/export-pdf', [EnergiController::class, 'exportPdf'])->name('divisi.export.pdf');
    Route::get('/laporan/export-chart-pdf', [EnergiController::class, 'exportChartToPDF'])->name('divisi.export.chart_pdf');
});

// USER UMUM
// USER UMUM
Route::middleware(['auth', 'verified', 'role:user_umum'])->group(function () {
    Route::get('/umum/dashboard', function () {
        return view('dashboard.umum');
    });

    // ✅ Tambahan baru: akses ke list energi
    Route::get('/umum/energi', [EnergiController::class, 'index'])->name('umum.energi.index');

    Route::get('/umum/energi/create', [EnergiController::class, 'create'])->name('umum.energi.create');
    Route::post('/umum/energi', [EnergiController::class, 'store'])->name('umum.energi.store');

    Route::get('/umum/laporan', [EnergiController::class, 'laporan'])->name('umum.laporan');
    Route::get('/umum/laporan/export-excel', [EnergiController::class, 'exportExcel'])->name('umum.export.excel');
    Route::get('/umum/laporan/export-pdf', [EnergiController::class, 'exportPdf'])->name('umum.export.pdf');
});


// Route dashboard otomatis berdasarkan role
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $role = Auth::user()->role;

    if ($role === 'super_user') {
        return view('dashboard.admin');
    } elseif ($role === 'divisi_user') {
        return view('dashboard.divisi');
    } else {
        return view('dashboard.umum');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Profil
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil/upload', [ProfilController::class, 'uploadFoto'])->name('profil.upload');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
});

// Lupa Password
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// REGISTER
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
