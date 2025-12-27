<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\AttendanceController;


Route::get('/hostels/{hostel}/buildings', [APIController::class, 'getBuildings']);
Route::get('/buildings/{building}/floors', [APIController::class, 'getFloors']);
Route::get('/buildings/{building}/rooms', [APIController::class, 'getRooms']);

Route::post("/biometric/attendance", [AttendanceController::class, 'api_biometric_attendance']);
Route::get("/biometric/attendance", [AttendanceController::class, 'api_biometric_attendance']);



