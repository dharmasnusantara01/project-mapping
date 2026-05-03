<?php

use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicMapController;
use Illuminate\Support\Facades\Route;

// ───── Public ─────────────────────────────────────────────────────────────
Route::get('/',                  [PublicMapController::class, 'index'])->name('public.map');
Route::get('/api/public/projects', [PublicMapController::class, 'projects'])->name('public.projects');

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
        Route::get('/', fn () => redirect()->route('admin.projects.index'))->name('home');

        Route::get('/projects',                    [AdminProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create',             [AdminProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects',                   [AdminProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit',     [AdminProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}',          [AdminProjectController::class, 'update'])->name('projects.update');
        Route::post('/projects/{project}/publish', [AdminProjectController::class, 'publish'])->name('projects.publish');


    });
