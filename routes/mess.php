<?php

use App\Http\Controllers\Dashboard\MessDashboardController;

Route::middleware(['auth', 'role:Mess Manager'])->group(function () {
    Route::get('/mess/dashboard', [MessDashboardController::class, 'index'])->name('dashboard');

    // Add other mess manager-specific routes here
});

