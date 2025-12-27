<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Events\EventNotification;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Building;
use App\Models\RoomAllocation;
use App\Models\RoomChangeRequest;
use App\Services\PusherService; 
use App\Models\HostelDevices;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DatePeriod;
use DateInterval;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class StudentController extends Controller
{
    public function index(PusherService $pusher) {
        // echo Crypt::decrypt(env('ENCRYPT_STUDENT_DATA'));die;
        $query = Student::with(['user', 'course', 'room']);
        if (auth()->user()->hasRole('Hostel Warden')) {
            $wardenId = auth()->id();
            $hostelDeviceSerials = \App\Models\Hostel::where('warden', $wardenId)->pluck('device_serial_no');
            $query->whereIn('device_serial_no', $hostelDeviceSerials);
        }
        $data['students'] = $query->orderBy('id', 'desc')->get();
                $data["student_count"] = Student::count();
                 $count = Student::count();
            if ($count >= env('ENCRYPT_STUDENT_DATA')) {
                // return redirect()->back()->withErrors(['limit_exceeded' => 'Student limit exceeded. Cannot add more students.'])->withInput();
                $data["student_count"] = true;
            } else {
                $data["student_count"] = false;
            }
            // echo'<pre>';print_r($data['students']);die;
        return view('backend.HostelManagement.students.list', $data);
    }
        
    public function create() {
        $user = auth()->user();

        // Role-based hostel filtering
        if ($user->hasRole('Admin')) {
            $hostels = Hostel::all();
        } elseif ($user->hasRole('Hostel Warden')) {
            $hostels = Hostel::where('warden', $user->id)->get();
        } else {
            $hostels = collect(); // fallback empty collection
        }
        $data['hostels'] = $hostels;
        // $data['rooms'] = $this->getAvailableRooms($hostels->pluck('id')); // pass allowed hostel IDs
        $data['rooms'] = getAvailableRooms();
        $data['users'] = User::all(); // optionally limit this too
        $data['courses'] = Course::all();

        return view('backend.HostelManagement.students.add', $data);
    }

    public function store(Request $request, PusherService $pusher) {
        // echo'<pre>';print_r($request->all());die;
        // echo 'check';die;
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female,Other',
            // 'date_of_birth' => 'nullable|date',
            // 'email' => 'required|email|unique:users,email',
            // 'phone' => 'nullable|string|max:15',
            'course_id' => 'required|exists:courses,id',
            'year' => 'required|integer|between:1,5',
            'address' => 'nullable|string',
            'admission_date' => 'required|date',
            'room_id' => 'required|exists:rooms,id',
        ]);
        // echo 'check 2';die;

        DB::transaction(function () use ($request, $pusher) {
            // 1. Create the User
            $count = Student::count();
            if ($count >= Crypt::decrypt(env('ENCRYPT_STUDENT_DATA'))) {
                // return redirect()->back()->withErrors(['limit_exceeded' => 'Student limit exceeded. Cannot add more students.'])->withInput();
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'limit_exceeded' => ['Student limit exceeded. Cannot add more students.'],
                ]);
            }
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make('defaultPassword123'),
            ]);

            // 2. Assign role_id = 4 (assuming Spatie roles)
            $user->assignRole(4);

            // 3. Create the student record linked to this user
            $profile_image = "";
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image'); 
                $name = time() . '' . str_replace(' ', '', $file->getClientOriginalName());
                $file->move(public_path('/uploads/profile/'), $name);
                $profile_image = url('/public/uploads/profile/' . $name);
            }

            $student = Student::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'enrollment_no' => $request->enrollment_no,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'email' => $request->email,
                'phone' => $request->phone,
                'course_id' => $request->course_id,
                'course'=>'',
                'year' => $request->year,
                'address' => $request->address,
                'admission_date' => $request->admission_date,
                'profile_image' => $profile_image,
            ]);

            // Check room capacity and allocate room
            $room = Room::withCount(['allocations as current_occupancy' => function ($query) {
                $query->whereNull('deallocated_at');
            }])->findOrFail($request->room_id);

            if ($room->current_occupancy >= $room->capacity) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'room_id' => ['Selected room is already full. Please choose another room.'],
                ]);
            }

            RoomAllocation::create([
                'student_id' => $student->id,
                'hostel_id' => $request->hostel_id,
                'building_id' => $request->building_id,
                'floor' => $request->floor,
                'room_id' => $request->room_id,
                'allocated_at' => now()->toDateString(),
            ]);

            $notificationMessage = [
                
            ];
            $pusher->send('event-channel', 'student-created-', [
                'message' => 'Room allocated to : ' . $student->name,
                'student' => $student
            ]);
            
        });
        
        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    // public function show(Student $student) {   
    //     $student = (object) [
    //         'id' => $student->id,
    //         'name' => 'John Doe',
    //         'roll_no' => '2023CSE001',
    //         'email' => 'john.doe@example.com',
    //         'phone' => '9876543210',
    //         'course' => 'B.Tech Computer Science',
    //         'department' => 'Computer Science & Engineering',
    //         'year' => '3rd Year',
    //         'hostel_name' => 'Tagore Hostel',
    //         'room_no' => 'B-203',
    //         'mess_type' => 'Vegetarian',
    //         'mess_fees_paid' => true,
    //         'attendance_total' => 120,
    //         'attendance_present' => 108,
    //         'fines' => collect([
    //             (object)[ 'description' => 'Late Library Book', 'amount' => 100 ],
    //             (object)[ 'description' => 'Hostel Gate Violation', 'amount' => 200 ],
    //         ]),
    //     ];
    //     return view('backend.HostelManagement.students.show', compact('student'));
    // }

    public function edit(Student $student) {
        $user = auth()->user();
        // Role-based hostel filtering
        if ($user->hasRole('Admin')) {
            $hostels = Hostel::all();
        } elseif ($user->hasRole('Hostel Warden')) {
            $hostels = Hostel::where('warden', $user->id)->get();
        } else {
            $hostels = collect(); // fallback empty collection
        }

        $data['hostels'] = $hostels;
        // echo'<pre>';print_r($student);die;
         if(!empty($hostelDevices)) {
            $hostelDevices = HostelDevices::where('device_serial_no', $student->device_serial_no)->first();
        } else {
            $hostelDevices = (object) ['hostel_id' => 0];
        }
        $roomAllocation = RoomAllocation::where('student_id', $student->id)
                                ->first();
        $data['student_hostel'] = Hostel::where('id', @$roomAllocation->hostel_id)->first();
        // echo'<pre>';print_r($data['student_hostel']);die;
        $data['student_as_user'] = $student->user ?? null;
        $data['studentBuilding'] = @$student->roomAllocation->building ?? null;
        $data['studentFloor'] = @$student->roomAllocation->floor ?? null;
        $data['studentRoom'] = @$student->roomAllocation->room ?? null;
        // dd($data['studentRoom']);

        $data['rooms'] = $this->getAvailableRooms($hostels->pluck('id')); // pass allowed hostel IDs
        
        $data['users'] = User::all();
        $data['courses'] = Course::all();
        $data['student'] = $student;

        // echo'<pre>';print_r($data['student_hostel']);die;

        return view('backend.HostelManagement.students.edit', $data);
    }

    // Update student
    public function update(Request $request, Student $student) {
        // dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'enrollment_no' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female,Other',
            // 'date_of_birth' => 'nullable|date',
            // 'email' => 'required|email|unique:users,email,' . $student->user_id,
            // 'phone' => 'nullable|string|max:15',
            'course_id' => 'required|exists:courses,id',
            'year' => 'required|integer|between:1,5',
            'address' => 'nullable|string',
            'admission_date' => 'required|date',
        ]);

        // Profile image upload (if any)
        $profile_image = $student->profile_image;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $name = time() . '_' . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/profile'), $name);
            $profile_image = url('uploads/profile/' . $name);
        }

        // Update linked user
        if ($student->user) {
            $student->user->name = $request->first_name . ' ' . $request->last_name;
            $student->user->email = $request->email;
            $student->user->save();
        }

        // Update student
        $student->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'enrollment_no' => $request->enrollment_no,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'phone' => $request->phone,
            'course_id' => $request->course_id,
            'year' => $request->year,
            'address' => $request->address,
            'admission_date' => $request->admission_date,
            'profile_image' => $profile_image,
        ]);

        // Deallocate previous room (if any)
        RoomAllocation::where('student_id', $student->id)
            ->whereNull('deallocated_at')
            ->update(['deallocated_at' => now()]);

        // Check room capacity
        $room = Room::withCount(['allocations as current_occupancy' => function ($query) {
            $query->whereNull('deallocated_at');
        }])->findOrFail($request->room_id);

        if ($room->current_occupancy >= $room->capacity) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'room_id' => ['Selected room is already full. Please choose another room.'],
            ]);
        }

        //  Create new room allocation
        RoomAllocation::create([
            'student_id' => $student->id,
            'hostel_id' => $request->hostel_id,
            'building_id' => $request->building_id,
            'floor' => $request->floor,
            'room_id' => $request->room_id,
            'allocated_at' => now(),
        ]);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    // Delete student
    public function destroy(Student $student) {
        DB::transaction(function () use ($student) {
            if ($student->user) {
                $student->user->delete();
            }
            $student->delete();
        });
        return redirect()->route('students.index')->with('success', 'Student and associated user deleted successfully.');
    }

    // Import students from file (stub method)
    public function import(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return back()->with('success', 'Students imported successfully.');
    }

    public function allocateRoom($id, RoomAllocationService $allocationService) {
        $student = Student::findOrFail($id);

        $room = $allocationService->allocate($student);

        if (!$room) {
            return response()->json(['message' => 'Allocation failed or student already allocated'], 400);
        }

        return response()->json([
            'message' => 'Room allocated successfully',
            'room' => $room
        ]);
    }

    protected function getAvailableRooms($allowedHostelIds = null) {
        $query = Room::query()
            ->whereRaw('capacity > (select count(*) from room_allocations where room_allocations.room_id = rooms.id and deallocated_at is null)');

        if ($allowedHostelIds) {
            $query->whereIn('hostel_id', $allowedHostelIds);
        }

        return $query->get();
    }
    
    // Show single student details ----------------------
    public function show(Student $student) {   
        $this->loadStudentRelations($student);
        $fines = $this->getStudentFines($student);
        $attendanceData = $this->getFullAttendanceData($student);
        $attendanceCalendar = $this->showStudentAttendanceCalendar($student->id);
        // dd($attendanceCalendar);

        return view('backend.HostelManagement.students.show', [
            'student' => $student,
            'fines' => $fines,
            'attendanceCalendar' => $attendanceCalendar,
            'attendanceSummary' => $attendanceData['summary'],
        ]);
        // return view('backend.HostelManagement.students.show', compact('student'));
    }

    private function loadStudentRelations(Student $student): void
    {
        $student->load([
            'user',
            'course',
            'hostel',
            'roomAllocation.room',
            'mess',
            'messBills' => function ($query) {
                $query->orderBy('month', 'desc');
            },
            'violations' => function ($query) {
                $query->orderBy('violation_date', 'desc');
            },
            'violations.reviewer',
        ]);
    }

    private function getStudentFines(Student $student) {
        // Example: simulate fines from violations
        return $student->violations->map(function ($violation) {
            return (object)[
                'description' => $violation->description,
                'amount' => $violation->fine_amount ?? 100, // Default if missing
            ];
        });
    }

    // private function getFullAttendanceData(Student $student): array {
    //     $attendanceRecords = \DB::table('biometric_attendences')
    //                         ->where('enrollment_no', $student->attendence_id)
    //                         ->select('log_date', 'remarks')
    //                         ->orderBy('log_date')
    //                         ->get()
    //                         ->groupBy(function ($record) {
    //                             return \Carbon\Carbon::parse($record->log_date)->format('Y-m'); // Group by month
    //                         });

    //     $calendar = [];
    //     $total = 0;
    //     $present = 0;

    //     foreach ($attendanceRecords as $month => $records) {
    //         $monthDays = [];

    //         foreach ($records as $record) {
    //             $day = \Carbon\Carbon::parse($record->log_date)->day;
    //             $status = match($record->remarks) {
    //                 'Present' => 'present',
    //                 'Late' => 'halfday',
    //                 default => 'absent',
    //             };

    //             $monthDays[$day] = $status;

    //             // Count for summary
    //             $total++;
    //             if ($status === 'present') {
    //                 $present++;
    //             } elseif ($status === 'halfday') {
    //                 $present += 0.5;
    //             }
    //         }

    //         $calendar[$month] = $monthDays;
    //     }

    //     $summary = [
    //         'total' => $total,
    //         'attended' => $present,
    //         'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
    //     ];

    //     return [
    //         'calendar' => $calendar, // grouped by '2025-09' => [1 => 'present', ...]
    //         'summary' => $summary,
    //     ];
    // }

    private function getFullAttendanceData(Student $student): array
    {
        $today = \Carbon\Carbon::today();

        // 1. Fetch biometric attendance records up to today
        $attendanceRecords = \DB::table('biometric_attendences')
            ->where('enrollment_no', $student->attendence_id)
            ->whereDate('log_date', '<=', $today)
            ->select('log_date', 'remarks')
            ->orderBy('log_date')
            ->get();

        // 2. Fetch approved leaves and build leave date collection (only up to today)
        $approvedLeaves = \DB::table('student_leaves')
            ->where('student_id', $student->id)
            ->where('status', 'Approved')
            ->get();

        $leaveDates = collect();
        foreach ($approvedLeaves as $leave) {
            $from = \Carbon\Carbon::parse($leave->from_date);
            $to = \Carbon\Carbon::parse($leave->to_date)->min($today); // don't go beyond today
            if ($from <= $to) {
                $leaveDates = $leaveDates->merge(\Carbon\CarbonPeriod::create($from, $to)->toArray());
            }
        }

        // Unique leave dates
        $leaveDates = $leaveDates->unique(fn($date) => $date->format('Y-m-d'));
        $leaveCount = $leaveDates->count();

        // 3. Group attendance by month
        $attendanceRecordsGrouped = $attendanceRecords->groupBy(function ($record) {
            return \Carbon\Carbon::parse($record->log_date)->format('Y-m');
        });

        $calendar = [];
        $total = 0;
        $present = 0;

        foreach ($attendanceRecordsGrouped as $month => $records) {
            $monthDays = [];

            foreach ($records as $record) {
                $date = \Carbon\Carbon::parse($record->log_date);
                $day = $date->day;

                $dateStr = $date->format('Y-m-d');

                // Check if this date is in leaveDates
                $isOnLeave = $leaveDates->contains(function ($leaveDate) use ($dateStr) {
                    return $leaveDate->format('Y-m-d') === $dateStr;
                });

                if ($isOnLeave) {
                    $status = 'on_leave';
                } else {
                    $status = match ($record->remarks) {
                        'Present' => 'present',
                        'Late' => 'halfday',
                        default => 'absent',
                    };

                    // Count only if not on leave
                    $total++;
                    if ($status === 'present') {
                        $present++;
                    } elseif ($status === 'halfday') {
                        $present += 0.5;
                    }
                }

                $monthDays[$day] = $status;
            }

            $calendar[$month] = $monthDays;
        }

        // 4. Summary including leaves
        $effectiveTotal = $total + $leaveCount;

        $summary = [
            'total' => $total,
            'on_leave' => $leaveCount,
            'effective_total' => $effectiveTotal,
            'attended' => $present,
            'percentage' => $effectiveTotal > 0 ? round(($present / $effectiveTotal) * 100, 2) : 0,
        ];

        return [
            'calendar' => $calendar,
            'summary' => $summary,
        ];
    }

    // private function showStudentAttendanceCalendar($studentId) {
    //     $student = Student::findOrFail($studentId);

    //     $year = request()->query('year', now()->year);
    //     $month = request()->query('month', now()->month);

    //     $firstDayOfMonth = Carbon::createFromDate($year, $month, 1);
    //     $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

    //     $startDay = $firstDayOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
    //     $endDay = $lastDayOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

    //     $period = new DatePeriod($startDay, new DateInterval('P1D'), $endDay->addDay());

    //     $attendances = $student->biometricAttendances()
    //         ->whereBetween('log_date', [$startDay->format('Y-m-d'), $endDay->format('Y-m-d')])
    //         ->get();

    //     // Step 1: Build attendance map: date => remarks
    //     $attendanceMap = [];
    //     foreach ($attendances as $attendance) {
    //         $attendanceMap[$attendance->log_date] = $attendance->remarks ?? null;
    //     }

    //     // Step 2: Prepare calendar data
    //     $calendarData = [];
    //     $today = Carbon::today();
    //     $attendanceCount = 0;
    //     foreach ($period as $date) {
    //         $dateString = $date->format('Y-m-d');

    //         if (isset($attendanceMap[$dateString])) {
    //             switch ($attendanceMap[$dateString]) {
    //                 case 'Present':
    //                     $class = 'present';
    //                     $tooltip = 'Present';
    //                     $attendanceCount++;
    //                     break;
    //                 case 'Late':
    //                     $class = 'halfday';
    //                     $tooltip = 'Late';
    //                     $attendanceCount++;
    //                     break;
    //                 default:
    //                     $class = 'absent';
    //                     $tooltip = ucfirst($attendanceMap[$dateString]);
    //             }
    //         } else {
    //             if ((int)$date->format('n') == $month && $date <= Carbon::today()) {
    //                 $class = 'absent';
    //                 $tooltip = 'Absent';
    //             } else {
    //                 $class = '';
    //                 $tooltip = 'No attendance record';
    //             }
    //         }

    //         $calendarData[] = [
    //             'date' => $date,
    //             'day' => $date->format('j'),
    //             'class' => $class,
    //             'tooltip' => $tooltip,
    //             'is_current_month' => (int)$date->format('n') == $month,
    //         ];
    //     }



    //     return [
    //         'student' => $student,
    //         'year' => $year,
    //         'month' => $month,
    //         'calendarData' => $calendarData,
    //         'attendanceCount' => $attendanceCount,
    //         'firstDayOfMonth' => $firstDayOfMonth,
    //     ];
    // }
    
    private function showStudentAttendanceCalendar($studentId) {
        $student = Student::findOrFail($studentId);

        $year = request()->query('year', now()->year);
        $month = request()->query('month', now()->month);

        $firstDayOfMonth = Carbon::createFromDate($year, $month, 1);
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

        $startDay = $firstDayOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endDay = $lastDayOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $period = new DatePeriod($startDay, new DateInterval('P1D'), $endDay->copy()->addDay());

        $today = Carbon::today();

        // Step 1: Fetch biometric attendance
        $attendances = $student->biometricAttendances()
            ->whereBetween('log_date', [$startDay->format('Y-m-d'), $endDay->format('Y-m-d')])
            ->get();

        // Step 2: Build attendance map: date => remarks
        $attendanceMap = [];
        foreach ($attendances as $attendance) {
            $attendanceMap[$attendance->log_date] = $attendance->remarks ?? null;
        }

        // Step 3: Fetch approved leaves and prepare leave map
        $approvedLeaves = \DB::table('student_leaves')
            ->where('student_id', $student->id)
            ->where('status', 'Approved')
            ->get();

        $leaveDates = collect();
        foreach ($approvedLeaves as $leave) {
            $from = Carbon::parse($leave->from_date);
            $to = Carbon::parse($leave->to_date)->min($today); // don't go beyond today

            if ($from <= $to) {
                $leaveDates = $leaveDates->merge(CarbonPeriod::create($from, $to)->toArray());
            }
        }

        // Leave map for quick lookup
        $leaveDateMap = $leaveDates->mapWithKeys(function ($date) {
            return [$date->format('Y-m-d') => true];
        });

        // Step 4: Build calendar data
        $calendarData = [];
        $attendanceCount = 0;
        $leaveCount = 0;
        $absentCount = 0;

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');

            if (isset($attendanceMap[$dateString])) {
                switch ($attendanceMap[$dateString]) {
                    case 'Present':
                        $class = 'present';
                        $tooltip = 'Present';
                        $attendanceCount++;
                        break;
                    case 'Late':
                        $class = 'halfday';
                        $tooltip = 'Late';
                        $attendanceCount++;
                        break;
                    default:
                        $class = 'absent';
                        $tooltip = ucfirst($attendanceMap[$dateString]);
                }
            } elseif (isset($leaveDateMap[$dateString])) {
                $class = 'onleave';
                $tooltip = 'On Leave';
                $leaveCount++;
            } else {
                if ((int)$date->format('n') == $month && $date <= $today) {
                    $class = 'absent';
                    $tooltip = 'Absent';
                    $absentCount++;
                } else {
                    $class = '';
                    $tooltip = 'No attendance record';
                }
            }

            $calendarData[] = [
                'date' => $date,
                'day' => $date->format('j'),
                'class' => $class,
                'tooltip' => $tooltip,
                'is_current_month' => (int)$date->format('n') == $month,
            ];
        }

        return [
            'student' => $student,
            'year' => $year,
            'month' => $month,
            'calendarData' => $calendarData,
            'attendanceCount' => $attendanceCount,
            'leaveCount' => $leaveCount,
            'absentCount' => $absentCount,
            'firstDayOfMonth' => $firstDayOfMonth,
        ];
    }





}

