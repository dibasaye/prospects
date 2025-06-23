<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Prospect management routes
    Route::resource('prospects', ProspectController::class);
    
    // Site management routes
    Route::resource('sites', SiteController::class);
    Route::get('/sites/{site}/lots', [SiteController::class, 'lots'])->name('sites.lots');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
