<?php

use App\Http\Controllers\Admin\AccountManagerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InstansiController;
use App\Http\Controllers\Admin\WitelController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicMapController;
use Illuminate\Support\Facades\Route;

// ───── Public ─────────────────────────────────────────────────────────────
Route::get('/',                    [PublicMapController::class, 'index'])->name('public.map');
Route::get('/api/public/instansi', [PublicMapController::class, 'instansi'])->name('public.instansi');

// ───── Auth ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ───── Admin (auth) ───────────────────────────────────────────────────────
Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/instansi',                     [InstansiController::class, 'index'])->name('instansi.index');
        Route::get('/instansi/import',              [InstansiController::class, 'importForm'])->name('instansi.import');
        Route::post('/instansi/import',             [InstansiController::class, 'import'])->name('instansi.import.process');
        Route::get('/instansi/create',              [InstansiController::class, 'create'])->name('instansi.create');
        Route::post('/instansi',                    [InstansiController::class, 'store'])->name('instansi.store');
        Route::get('/instansi/{instansi}/edit',     [InstansiController::class, 'edit'])->name('instansi.edit');
        Route::put('/instansi/{instansi}',          [InstansiController::class, 'update'])->name('instansi.update');
        Route::post('/instansi/{instansi}/publish', [InstansiController::class, 'publish'])->name('instansi.publish');

        Route::resource('witel', WitelController::class)->except(['show']);
        Route::resource('account-managers', AccountManagerController::class)
            ->parameter('account-managers', 'accountManager')
            ->except(['show'])
            ->names('account_managers');
    });
