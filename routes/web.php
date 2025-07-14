<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnergiController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfilController;
// use App\Http\Controllers\ChartController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OtpController; 

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

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

// SUPER USER ROUTES
Route::middleware(['auth', 'verified', 'role:super_user', 'no_cache'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.admin');
    })->name('dashboard');


    
    // Manajemen Pengguna (Users)
    Route::resource('users', UserController::class);

    // Manajemen Energi (CRUD)
    // Menggunakan Route::resource untuk CRUD yang lebih ringkas
    Route::resource('energi', EnergiController::class)->except(['show']); // show tidak ada di controller Anda
    Route::post('/energi/import', [EnergiController::class, 'import'])->name('energi.import');
    Route::get('/energi/template', [EnergiController::class, 'downloadTemplate'])->name('energi.template');

    // Laporan Energi untuk Super User
    Route::get('/laporan', [EnergiController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/json', [EnergiController::class, 'laporanJson'])->name('laporan.json');
    Route::get('/laporan/export-pdf', [EnergiController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/laporan/export-excel', [EnergiController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-chart-pdf', [EnergiController::class, 'exportChartToPDF'])->name('laporan.export-chart-pdf');
    // Rute '/export-energi' yang asli sekarang menjadi bagian dari '/admin/laporan/export-excel'
});


// Route global tanpa prefix, bisa diakses semua role
Route::middleware(['auth'])->group(function () {
    Route::post('/energi/import', [EnergiController::class, 'import'])->name('energi.import');
    Route::get('/energi/export', [EnergiController::class, 'exportExcel'])->name('energi.export');
});


// DIVISI USER ROUTES
Route::middleware(['auth', 'verified', 'role:divisi_user'])->prefix('divisi')->name('divisi.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.divisi');
    })->name('dashboard');

    // Manajemen Energi (CRUD) untuk Divisi
    Route::resource('energi', EnergiController::class)->except(['show', 'edit', 'update']);


    // Manajemen Pengguna (Divisi hanya melihat atau membuat?)
    Route::get('/users', [UserController::class, 'indexDivisi'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'createDivisi'])->name('users.create');
    Route::post('/users', [UserController::class, 'storeDivisi'])->name('users.store');

    // Laporan Energi untuk Divisi User
    Route::get('/laporan', [EnergiController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/export-excel', [EnergiController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [EnergiController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/laporan/export-chart-pdf', [EnergiController::class, 'exportChartToPDF'])->name('laporan.export-chart-pdf');
});

// USER UMUM ROUTES
Route::middleware(['auth', 'verified', 'role:user_umum'])->prefix('umum')->name('umum.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.umum');
    })->name('dashboard');

    // List Energi untuk User Umum (hanya melihat)
    Route::get('/energi', [EnergiController::class, 'index'])->name('energi.index');
    // Jika umum bisa membuat data energi:
    Route::get('/energi/create', [EnergiController::class, 'create'])->name('energi.create');
    Route::post('/energi', [EnergiController::class, 'store'])->name('energi.store');


    // Laporan Energi untuk User Umum (hanya melihat/export)
    Route::get('/laporan', [EnergiController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/export-excel', [EnergiController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [EnergiController::class, 'exportPdf'])->name('laporan.export-pdf');
});
// REGISTER
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
