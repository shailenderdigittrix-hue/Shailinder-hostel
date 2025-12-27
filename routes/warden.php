<?php

use App\Http\Controllers\Dashboard\WardenDashboardController;
use App\Http\Controllers\RoomChangeRequestController;

Route::middleware(['auth', 'role:Hostel Warden'])->group(function () {
    Route::get('/dashboard', [WardenDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/hostel/vacancy-status', [WardenDashboardController::class, 'hostel_room_vacancy_status'])->name('roomVacancyStatus');
    Route::get('/hostel/{id}/rooms', [WardenDashboardController::class, 'getRoomsByHostel'])->name('hostel.rooms');

    Route::get('/assigned-rooms',[WardenDashboardController::class, 'alloted_rooms_list'])->name('alloted_rooms_list');
    Route::get('/room-change-requests', [RoomChangeRequestController::class, 'room_change_requests_list'])->name('roomChangeRequestsList');
    Route::post('/room-change-requests', [RoomChangeRequestController::class, 'store'])->name('roomChangeRequests.store');


    
    

    
//     // Add other warden-specific routes here
});

// Route::middleware(['auth', 'role:Hostel Warden'])->prefix('warden')->name('warden.')->group(function () {
//     Route::get('/dashboard', [WardenDashboardController::class, 'index'])->name('dashboard');
// });