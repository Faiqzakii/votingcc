<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware('auth')->group(function () {
    Route::controller(\App\Http\Controllers\VoteController::class)->group(function () {
        Route::get('/vote', 'index')->name('vote');
        Route::post('/vote', 'store')->name('vote.store');
        Route::get('/vote/phase2', 'indexPhase2')->name('vote.phase2');
        Route::post('/vote/phase2', 'storePhase2')->name('vote.phase2.store');
        Route::get('/vote/done', 'done')->name('vote.done');
        Route::get('/vote/closed', 'closed')->name('vote.closed');
    });

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware(\App\Http\Middleware\EnsureIsAdmin::class)
        ->name('dashboard');
        
    Route::post('/dashboard/phase', [\App\Http\Controllers\DashboardController::class, 'updatePhase'])
        ->middleware(\App\Http\Middleware\EnsureIsAdmin::class)
        ->name('dashboard.update-phase');
        
    Route::post('/dashboard/generate-dummy', [\App\Http\Controllers\DashboardController::class, 'generateDummyVotes'])
        ->middleware(\App\Http\Middleware\EnsureIsAdmin::class)
        ->name('dashboard.dummy-votes');
        
    Route::get('/dashboard/export', [\App\Http\Controllers\DashboardController::class, 'export'])
        ->middleware(\App\Http\Middleware\EnsureIsAdmin::class)
        ->name('dashboard.export');

    Route::middleware(\App\Http\Middleware\EnsureIsAdmin::class)->group(function () {
        Route::get('users/template', [\App\Http\Controllers\UserController::class, 'downloadTemplate'])->name('users.template');
        Route::get('users/import', [\App\Http\Controllers\UserController::class, 'import'])->name('users.import');
        Route::post('users/import', [\App\Http\Controllers\UserController::class, 'storeImport'])->name('users.import.store');
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('candidates', \App\Http\Controllers\CandidateController::class);
    });
});
