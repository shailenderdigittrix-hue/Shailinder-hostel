<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\VerifyGlobalToken;

use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\WardenDashboardController;
use App\Http\Controllers\Dashboard\MessDashboardController;

use App\Http\Controllers\APIController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomChangeRequestController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\PermissionController;

use App\Http\Controllers\MessFoodItemController;
use App\Http\Controllers\MessController;
use App\Http\Controllers\MessBillController;

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\StudentLeaveController;

use App\Http\Controllers\DisciplinaryViolationController;
use App\Http\Controllers\ViolationTypeController;
use App\Http\Controllers\Apartment\CoupleApartmentController;
use App\Services\PusherService;
use App\Http\Controllers\NotificationController;


// Route::get('/', function () {
//     if (!Auth::check()) return redirect('/login');

//     $user = Auth::user();

//     if ($user->hasRole('Admin')) return redirect()->route('admin.dashboard');
//     if ($user->hasRole('Hostel Warden')) return redirect()->route('warden.dashboard');
//     if ($user->hasRole('Mess Manager')) return redirect()->route('mess.dashboard');
    
//     return redirect('/login');
// });

// Route::get('/login');

Route::middleware(['web', 'verifyGlobalToken', 'auth'])->group(function () {
    Route::get('/', function () {
        if (!Auth::check()) return redirect('/login');
        $user = Auth::user();

        if ($user->hasRole('Admin')) return redirect()->route('admin.dashboard');
        if ($user->hasRole('Hostel Warden')) return redirect()->route('warden.dashboard');
        if ($user->hasRole('Mess Manager')) return redirect()->route('mess.dashboard');

        return redirect('/login');
    });
});


Route::get('/test-pusher', function (PusherService $pusher) {
    $pusher->send('event-channel', 'notification-event', [
        'message' => 'Hello from PusherService'
    ]);

    return 'Event sent';
})->middleware(['verifyGlobalToken']);

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function() {
    Route::resource('users', UserController::class);
    Route::resource('permissions', PermissionController::class);
    
    Route::get('users/{id}/permissions', [UserController::class, 'permissionsEdit'])->name('users.permissions.edit');
    Route::put('users/{id}/permissions', [UserController::class, 'permissionsUpdate'])->name('users.permissions.update');
    
    Route::get('/room-change-requests', [RoomChangeRequestController::class, 'room_change_requests_list'])->name('roomChangeRequestsList');
    Route::post('/room-change-requests', [RoomChangeRequestController::class, 'store'])->name('roomChangeRequests.store');

    // Couple
    // Route::get("/add-apartment", [CoupleApartmentController::class, 'add'])->name('apartment.add');
   Route::resource('couple-apartment', CoupleApartmentController::class);


});

// Shared routes (all roles)
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('backend/users/profile');
    })->name('profile');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');


    // Settings Module ---------------------------------------------- 
    Route::resource('violation-types', ViolationTypeController::class);
    Route::resource('email-templates', EmailTemplateController::class);
    Route::resource('hostels', HostelController::class);
    Route::resource('students', StudentController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('violations', DisciplinaryViolationController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('notifications', NotificationController::class);
    
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    
    Route::get('/rooms/alloted-rooms', [RoomController::class, 'get_alloted_rooms'])->name('room.alloted');
    Route::get('/room-change-requests', [RoomChangeRequestController::class, 'room_change_requests_list'])->name('roomChangeRequestsList');
    Route::post('/room-change-requests', [RoomChangeRequestController::class, 'store'])->name('roomChangeRequests.store');


    // Web APIs Starts --------------------------------------------
    Route::get('/hostels/{hostel}/buildings', [APIController::class, 'getBuildings']);
    Route::get('/buildings/{building}/floors', [APIController::class, 'getFloors']);
    Route::get('/buildings/{building}/rooms', [APIController::class, 'getRooms']);
    // Web APIs Ends ----------------------------------------------


    Route::get('/mess/food-list', [MessFoodItemController::class, 'index'])->name('fooditems.list');
    Route::get('/mess/create-food-view', [MessFoodItemController::class, 'create'])->name('fooditems.create');
    Route::get('/mess/edit-food-view/{id}', [MessFoodItemController::class, 'edit'])->name('fooditems.edit');
    Route::post('/mess/food-item-create', [MessFoodItemController::class, 'store'])->name('fooditems.store');
    Route::put('/mess/food-item-update/{id}', [MessFoodItemController::class, 'update'])->name('fooditems.update');
    Route::delete('/mess/food-item-delete/{id}', [MessFoodItemController::class, 'destroy'])->name('fooditems.destroy');

    // Mess Management -------------------------------------------
    Route::get('/mess/create', [MessController::class, 'create'])->name('mess.create');
    Route::post('/mess/store', [MessController::class, 'store'])->name('mess.store');
    Route::get('/mess/list', [MessController::class, 'index'])->name('mess.list');
    Route::get('/mess/edit/{id}', [MessController::class, 'edit'])->name('mess.edit');
    Route::put('/mess/update/{id}', [MessController::class, 'update'])->name('mess.update');

    Route::delete('/mess/destroy/{id}', [MessController::class, 'destroy'])->name('mess.destroy');

    // Mess Billing ----------------------------------------------
    Route::get('/mess-bills', [MessBillController::class, 'index'])->name('mess.bills');
    Route::put('/mess-bills/{bill}/mark-paid', [MessBillController::class, 'markPaid'])->name('mess.bills.markPaid');
    Route::get('/generate-mess-bills/{month}', [MessBillController::class, 'generateBills'])->name('mess.bills.generate');
    // php artisan mess:generate-bills --month=2025-10

    // End Mess Management ---------------------------------------

    //  Attendence -----------------------------------------------
    Route::get("/attendence/list", [AttendanceController::class, 'index'])->name('attendence.list');
    Route::get('/attendance/daily-report', [AttendanceController::class, 'dailyReport'])->name('attendance.dailyReport');
    Route::get('/attendance/monthly-report', [AttendanceController::class, 'monthlyReport'])->name('attendance.monthlyReport');
    // End Attence -----------------------------------------------
    
    // Student Leave Module Start --------------------------------
    Route::resource('student-leaves', StudentLeaveController::class);
    Route::post('student-leaves/{id}/approve', [StudentLeaveController::class, 'approve'])->name('student-leaves.approve');
    Route::post('student-leaves/{id}/reject', [StudentLeaveController::class, 'reject'])->name('student-leaves.reject');
    Route::get('student-leaves-export', [StudentLeaveController::class, 'exportExcel'])->name('student-leaves.export')->middleware('role:Admin|Hostel Warden');
    // Student Leave Module End ----------------------------------

    

    

    

});


// View Holiday Calendor Akshat working
  Route::get("/holiday", [HolidayController::class, 'index'])->name('holiday.index');
//   https://yourdomain.com/api/biometric/attendance
// End Holiday Calendor



