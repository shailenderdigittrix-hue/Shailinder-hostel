<?php

use App\Http\Controllers\Dashboard\AdminDashboardController;

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
   
    Route::get('/admin/roles', [AdminDashboardController::class, 'role_list'])->name('roles.list');
    Route::get('/admin/role-add', [AdminDashboardController::class, 'role_add'])->name('role.add');
    Route::post('/admin/role-save', [AdminDashboardController::class, 'role_save'])->name('admin.roleStore');
    // Route::get('/admin/role-edit/{$id}', [AdminDashboardController::class, 'role_edit'])->name('role.edit');
    Route::get('/admin/role-edit/{id}', [AdminDashboardController::class, 'role_edit'])->name('role.edit');
    Route::post('/admin/role-update', [AdminDashboardController::class, 'role_update'])->name('admin.roleUpdate');
    Route::post('/admin/role-destroy', [AdminDashboardController::class, 'role_delete'])->name('role.destroy');
    
    Route::get('/admin/role-permissions/{id}', [AdminDashboardController::class, 'role_permissions'])->name('admin.role.permissions');
    Route::post('/admin/role-permissions/update', [AdminDashboardController::class, 'ajaxUpdatePermission'])->name('admin.role.permissions.update');

    Route::post("/admin/import-bulk-students", [AdminDashboardController::class, 'importBulkStudents'])->name('admin.importBulkStudents');
    Route::post('/import/room-allocations', [AdminDashboardController::class, 'importRoomAllocations'])->name('import.roomAllocations');
    Route::post('/admin/room/re-allocations/store', [AdminDashboardController::class, 'room_re_allocate'])->name('room.reAllocations.store');
    

    // Rooms Related 
    Route::get('/admin/assigned-rooms', [AdminDashboardController::class, 'alloted_rooms_list'])->name('admin.alloted_rooms_list');
    Route::get('/hostel/vacancy-status', [AdminDashboardController::class, 'hostel_room_vacancy_status'])->name('admin.roomVacancyStatus');
    Route::get('/hostel/{id}/rooms', [AdminDashboardController::class, 'getRoomsByHostel'])->name('hostel.rooms');
    
    Route::post('/admin/update-status', [AdminDashboardController::class, 'updateStatus'])->name('admin.updateStatus');

    Route::get('/admin/smtp/edit', [AdminDashboardController::class, 'edit_smtp'])->name('admin.editSMTP');
    Route::post('/admin/smtp/update/{id}', [AdminDashboardController::class, 'update_smtp'])->name('admin.updateSMTP');
    Route::get('/admin/test/data', [AdminDashboardController::class, 'test_data'])->name('admin.test_data');
    

    
    // Add other admin-specific routes here
});
