<?php

use App\Http\Controllers\Admin\AccountManagerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InstansiController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\WitelController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicMapController;
use Illuminate\Support\Facades\Route;

// ───── Public ─────────────────────────────────────────────────────────────
Route::get('/',                    [PublicMapController::class, 'index'])->name('public.map')->middleware('auth');
Route::get('/api/public/instansi', [PublicMapController::class, 'instansi'])->name('public.instansi');

// ───── Auth ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login/dashboard-vertikal',  [LoginController::class, 'show'])->name('login');
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
        Route::post('/account-managers/{accountManager}/test-telegram',
            [AccountManagerController::class, 'testTelegram'])
            ->name('account_managers.test_telegram');

        // Sales Pipeline Projects
        Route::get('/projects',                         [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create',                  [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects',                        [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/instansi/{instansi}/projects/create', [ProjectController::class, 'create'])->name('instansi.projects.create');
        Route::post('/instansi/{instansi}/projects',    [ProjectController::class, 'store'])->name('instansi.projects.store');
        Route::get('/projects/{project}/edit',          [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}',               [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}',            [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::post('/projects/{project}/advance',      [ProjectController::class, 'advance'])->name('projects.advance');
        Route::post('/projects/{project}/lost',         [ProjectController::class, 'lost'])->name('projects.lost');
        Route::get('/projects/{project}/file/{type}',   [ProjectController::class, 'file'])->name('projects.file');
    });
