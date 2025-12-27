<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Hostel;
use App\Models\HostelDevices;
use App\Models\Notification;
use App\Models\StudentLeave;
use App\Models\StudentAttendance;
use App\Models\BiometricAttendence;

use DB;
use App\Mail\DailyAttendanceReport;
use App\Mail\WrongDeviceAttendence;
use App\Mail\StudentLateComingNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Services\PusherService; 

// use Illuminate\Support\Facades\DB;



class AttendanceController extends Controller
{
    public function index(Request $request) {
        $query = BiometricAttendence::query();
       
        $from_date = !empty($request->from_date)
            ? Carbon::parse($request->from_date)->toDateString()
            : Carbon::now()->toDateString();

        $to_date = !empty($request->to_date)
            ? Carbon::parse($request->to_date)->toDateString()
            : Carbon::now()->toDateString();

        // Filter by date range
        if ($from_date && $to_date) {
            $query->whereBetween("log_date", [$from_date, $to_date]);
        }

        // Filter by month
        if ($request->filled("month")) {
            $month = $request->month;
            $query
                ->whereYear("log_date", substr($month, 0, 4))
                ->whereMonth("log_date", substr($month, 5, 2));
        }

        // Filter by enrollment_no
        if ($request->filled("search")) {
            $query->where("enrollment_no", $request->search);
        
        }

        // Filter by hostel
        if ($request->filled("device_serial_no")) {
            // echo $request->device_serial_no;die;
            $query->where('device_serial_no', $request->device_serial_no);
        };

        $attendenceCount = $query->count();
        $student_attendance = $query->with([
                "student",
                "student.user",
                "student.hostel",
                "student.course",
                // "student.course",
                "student.room",
            ])
            ->orderBy("log_date_time", "desc")
            ->paginate(10)
            ->appends($request->all());

        $student_attendance->getCollection()->transform(function ($e) {
            $time = Carbon::parse($e->log_date_time)->format("H:i:s");

            // Define your time thresholds
            $presentStart = "10:00:00";
            $presentEnd = "11:05:00";

            if ($time >= $presentStart && $time <= $presentEnd) {
                $e->entry_status = "Present";
            } elseif ($time >= $presentEnd) {
                $e->entry_status = "Late Coming";
            }
            //  else {
            //     $e->status = 'Absent';
            // }

            return $e;
        });

        // Attendance trends chart (optional)
        $attendanceCounts = BiometricAttendence::selectRaw(
                "DATE(log_date_time) as date, COUNT(*) as total"
            )
            ->where("log_date_time", ">=", now()->subDays(7))
            ->groupBy("date")
            ->orderBy("date")
            ->get();

        
        $presentStart = "10:00:00";
        $presentEnd = "11:05:00";

        // Start building the query
        $attendanceRecords = BiometricAttendence::select(
                "enrollment_no",
                "log_date_time"
            )->orderBy("log_date_time", "desc");
            // echo'<pre>3';print_r($attendanceRecords->get());die;

        // Filter by date range
        if ($from_date && $to_date) {
            $attendanceRecords->whereBetween("log_date", [
                $from_date,
                $to_date,
            ]);
        }

        // Filter by hostel
        if ($request->filled("device_serial_no")) {
            // echo $request->device_serial_no;die;
            $attendanceRecords->where('device_serial_no', $request->device_serial_no);
        };

        

        // Filter by month
        if ($request->filled("month")) {
            $month = $request->month; // Format expected: YYYY-MM
            $attendanceRecords
                ->whereYear("log_date_time", substr($month, 0, 4))
                ->whereMonth("log_date_time", substr($month, 5, 2));
        }

        // Filter by enrollment_no
        if ($request->filled("search")) {
            $attendanceRecords->where("enrollment_no", $request->search);
        }

        // Now execute query and process data
        $attendanceRecords = $attendanceRecords
            ->get()
            ->groupBy("enrollment_no")
            ->map(function ($records) use ($presentStart, $presentEnd) {
                
                // Get the latest log for each student
                $latest = $records->first();
                $time = Carbon::parse($latest->log_date_time)->format("H:i");

                // Determine entry status
                if ($time >= $presentStart && $time <= $presentEnd) {
                    $latest->entry_status = "Present";
                } elseif ($time > $presentEnd) {
                    $latest->entry_status = "Late Coming";
                } else {
                    $latest->entry_status = "Absent";
                }

                return $latest;
            });

        // echo'<pre>';print_r($attendanceRecords);die;
        
        // Example counts
        $lateCount = $attendanceRecords
            ->where("entry_status", "Late Coming")
            ->count();

        $date = $request->filled("date")
            ? Carbon::parse($request->date)->toDateString()
            : null;

        $totalStudents = Student::count();

        // Present count

        // Start query
        $presentQuery = Student::whereNotNull("attendence_id")
            ->join(
                "biometric_attendences",
                "students.attendence_id",
                "=",
                "biometric_attendences.enrollment_no"
            )
            ->whereNotNull("biometric_attendences.log_date_time"); // ensure not null

        // if ($request->filled('date')) {
        //     $presentQuery->whereDate('biometric_attendences.log_date_time', $date);
        // }

        // Filter by date range
        if ($from_date && $to_date) {
            $presentQuery->whereBetween("biometric_attendences.log_date", [
                $from_date,
                $to_date,
            ]);
        }

        // Optional month filter
        if ($request->filled("month")) {
            $month = $request->month; // Format: YYYY-MM
            $presentQuery
                ->whereYear(
                    "biometric_attendences.log_date_time",
                    substr($month, 0, 4)
                )
                ->whereMonth(
                    "biometric_attendences.log_date_time",
                    substr($month, 5, 2)
                );
        }

        // Apply time filter for Present
        $presentCount = $presentQuery
            ->whereTime(
                "biometric_attendences.log_date_time",
                ">=",
                $presentStart
            )
            ->whereTime(
                "biometric_attendences.log_date_time",
                "<=",
                $presentEnd
            )
            ->count();

        // Base query
        $lateQuery = Student::whereNotNull("attendence_id")
            ->join(
                "biometric_attendences",
                "students.attendence_id",
                "=",
                "biometric_attendences.enrollment_no"
            )
            ->whereNotNull("biometric_attendences.log_date_time");

        // Filter by date range
        if ($from_date && $to_date) {
            $lateQuery->whereBetween("biometric_attendences.log_date", [
                $from_date,
                $to_date,
            ]);
        }

        // Optional month filter
        if ($request->filled("month")) {
            $month = $request->month; // Format: YYYY-MM
            $lateQuery
                ->whereYear(
                    "biometric_attendences.log_date_time",
                    substr($month, 0, 4)
                )
                ->whereMonth(
                    "biometric_attendences.log_date_time",
                    substr($month, 5, 2)
                );
        }

        // Time filter for "Late Coming"
        $lateCount = $lateQuery
            ->whereTime(
                "biometric_attendences.log_date_time",
                ">=",
                $presentEnd
            )
            ->count();

        // Base query
        $absentQuery = Student::whereNotNull("attendence_id")->leftJoin(
            "biometric_attendences",
            "students.attendence_id",
            "=",
            "biometric_attendences.enrollment_no"
        );

        if ($from_date && $to_date) {
            $absentQuery->whereBetween("biometric_attendences.log_date", [
                $from_date,
                $to_date,
            ]);
        }

        // Optional month filter
        if ($request->filled("month")) {
            $month = $request->month; // Format: YYYY-MM
            $absentQuery
                ->whereYear("students.created_at", substr($month, 0, 4)) // or use log_date_time if needed
                ->whereMonth("students.created_at", substr($month, 5, 2));
        }

        // Final count
        // $absentCount = $absentQuery->count();

        $absentCount = $totalStudents - ($presentCount + $lateCount);
        // echo'<pre>';print_r($studentsnt_attendance);die;
        $today = \Carbon\Carbon::today()->toDateString();
        $absentStudents = Student::whereNotIn('enrollment_no', function($query) use ($today) {
                                $query->select('enrollment_no')
                                    ->from('biometric_attendences')
                                    ->where('log_date', $today);
                            })->get();
        
        $hostel = Hostel::all();

        $data = [
            "totalAttendenceEntries" => $attendenceCount,
            "totalStudents" => $totalStudents,
            "attendanceCounts" => $attendanceCounts,
            "totalStudents" => $totalStudents,
            "presentCount" => $presentCount,
            "lateCount" => $lateCount,
            "absentCount" => $absentCount,
            "device_serial_no" => $request->device_serial_no,
            "student_attendance" => $student_attendance,
            'hostels' => $hostel
        ];

        // $this->dailyAttandanceEmail();



        return view("backend.HostelManagement.Attendence.list", $data);
    }

    
    /**  Show the form for creating a new resource.  */
    public function create() {
        //
    }

    /**  * Store a newly created resource in storage.  */
    public function store(Request $request)
    {
        //
    }

    /** * Display the specified resource. */
    public function show(string $id)
    {
        //
    }

    /** * Show the form for editing the specified resource. */
    public function edit(string $id)
    {
        //
    }

    /** * Update the specified resource in storage. */
    public function update(Request $request, string $id)
    {
        //
    }

    /** * Remove the specified resource from storage.  */
    public function destroy(string $id)
    {
        //
    }

    public function dailyReport(Request $request) {
        // --- TIME RANGE ---
        $presentStart = "10:00:00";
        $presentEnd = "12:05";

        // --- DATE HANDLING ---
        $date = $request->filled("date")
            ? Carbon::parse($request->input("date"))->toDateString()
            : Carbon::today()->toDateString();

        // --- PRESENT COUNT ---
        $presentCount = Student::whereNotNull("attendence_id")
            ->join(
                "biometric_attendences",
                "students.attendence_id",
                "=",
                "biometric_attendences.enrollment_no"
            )
            ->whereNotNull("biometric_attendences.log_date_time")
            ->whereDate("biometric_attendences.log_date_time", $date)
            ->whereTime(
                "biometric_attendences.log_date_time",
                ">=",
                $presentStart
            )
            ->whereTime(
                "biometric_attendences.log_date_time",
                "<=",
                $presentEnd
            )
            ->count();

        // --- LATE COUNT ---
        $lateCount = Student::whereNotNull("attendence_id")
            ->join(
                "biometric_attendences",
                "students.attendence_id",
                "=",
                "biometric_attendences.enrollment_no"
            )
            ->whereNotNull("biometric_attendences.log_date_time")
            ->whereDate("biometric_attendences.log_date_time", $date)
            ->whereTime("biometric_attendences.log_date_time", ">", $presentEnd)
            ->count();

        // --- ABSENT COUNT ---
        $absentCount = Student::whereNotNull("attendence_id")
            ->leftJoin(
                "biometric_attendences",
                "students.attendence_id",
                "=",
                "biometric_attendences.enrollment_no"
            )
            ->where(function ($q) use ($date) {
                $q->whereNull(
                    "biometric_attendences.log_date_time"
                )->orWhereDate(
                    "biometric_attendences.log_date_time",
                    "!=",
                    $date
                );
            })
            ->count();

        // --- TOTAL STUDENTS ---
        $totalStudents = Student::whereNotNull("attendence_id")->count();

        // --- ATTENDANCE RECORDS ---
        $data["student_attendance"] = BiometricAttendence::whereDate(
            "log_date_time",
            $date
        )
            ->with([
                "student",
                "student.hostel",
                "student.course",
                "student.course",
                "student.room",
            ])
            ->orderBy("log_date_time", "asc")
            ->paginate(10);

        $data["student_attendance"]->getCollection()->transform(function ($e) {
            $time = Carbon::parse($e->log_date_time)->format("H:i");
            // Define your time thresholds
            $presentStart = "10:00:00";
            $presentEnd = "11:05:00";

            if ($time >= $presentStart && $time <= $presentEnd) {
                $e->entry_status = "Present";
            } elseif ($time > $presentEnd) {
                $e->entry_status = "Late Coming";
            }
            //  else {
            //     $e->status = 'Absent';
            // }

            return $e;
        });

        // echo '<pre>';print_r($data);die;

        // --- STATS DATA ---
        $data["date"] = $date;
        $data["presentCount"] = $presentCount;
        $data["lateCount"] = $lateCount;
        $data["absentCount"] = $absentCount;
        $data["totalStudents"] = $totalStudents;
        // --- RETURN VIEW ---
        return view("backend.HostelManagement.Attendence.daily_report", $data);
    }

    public function monthlyReport(Request $request) {
        $month = $request->input("month", now()->format("Y-m"));
        $data["student_attendance"] = BiometricAttendence::whereYear(
                "log_date",
                substr($month, 0, 4)
            )
            ->whereMonth("log_date", substr($month, 5, 2))
            ->with([
                "student",
                "student.hostel",
                "student.course",
                "student.room",
            ])
            ->orderBy("log_date_time")
            ->paginate(10)
            ->through(function ($e) {
                $time = Carbon::parse($e->log_date_time)->format("H:i:s");

                // Define your time thresholds
                $presentStart = "10:00:00";
                $presentEnd = "11:05:00";

                if ($time >= $presentStart && $time <= $presentEnd) {
                    $e->entry_status = "Present";
                } elseif ($time > $presentEnd) {
                    $e->entry_status = "Late Coming";
                }

                return $e;
            });

        $data["month"] = $month;
        // dd($data);
        return view( "backend.HostelManagement.Attendence.monthly_report", $data);
    }

    public function trends() {
        $attendanceCounts = BiometricAttendence::selectRaw(
            "DATE(log_date_time) as date, COUNT(*) as total"
        )
            ->where("log_date_time", ">=", now()->subDays(7))
            ->groupBy("date")
            ->orderBy("date")
            ->get();

        return view("attendance.trends", compact("attendanceCounts"));
    }

    // public function api_biometric_attendance(Request $request, PusherService $pusher) {
    //     // Log everything first (debug)
    //     Log::info('DEVICE HIT', [
    //         'headers' => $request->headers->all(),
    //         'body' => $request->all()
    //     ]);

    //     // Accept realtime logs only
    //     if ($request->header('request_code') !== 'realtime_glog') {
    //         return response('OK', 200);
    //     }

    //     $logDateTime = Carbon::createFromFormat(
    //         'YmdHis',
    //         $request->io_time
    //     );

    //     dd($request->all());
        
    //     $logDateTime = Carbon::createFromFormat(
    //         'YmdHis',
    //         $request->io_time
    //     );

    //     $logDateTime = Carbon::createFromFormat("m/d/Y H:i:s", $request->log_date_time)
    //         ->format("Y-m-d H:i:s");

    //     $logTime = date("H:i:s", strtotime($logDateTime));
    //     Log::info("Biometric logTime", ['log_time' => $logTime]);

    //     // Determine attendance remark
    //     $remark = null;
    //     $from = "10:00:00";
    //     $to = "11:00:00";
    //     if ($logTime) {
    //         if ($logTime >= $from && $logTime <= $to) {
    //             $remark = "Present";
    //         } elseif ($logTime > $to) {
    //             $remark = "Late";
    //         } else {
    //             $remark = "Absent"; // Optional: could also be "Too Early"
    //         }
    //     }

    //     Log::info("Biometric remark", ['remark' => $remark]);
    //     DB::beginTransaction();

    //     // Parse download datetime
    //     $downloadDateTime = Carbon::createFromFormat("m/d/Y H:i:s", $request->download_date_time)->format("Y-m-d H:i:s");

    //     // Extract date and time
    //     $logDate = Carbon::parse($logDateTime)->toDateString();
    //     $logTime = Carbon::parse($logDateTime)->toTimeString();

    //     Log::info("Check data", [
    //         "logDateTime" => $logDateTime,
    //         "downloadDateTime" => $downloadDateTime,
    //     ]);

    //     // Check if attendance already exists
    //     $checkAttendanceToday = BiometricAttendence::whereDate("log_date", $logDate)
    //         ->where("enrollment_no", $request->enrollment_no)
    //         ->first();

    //     if (empty($checkAttendanceToday)) {
    //         // Check if student exists
    //         $checkStudent = Student::with('roomAllocation')
    //             ->where("attendence_id", $request->enrollment_no)
    //             ->first();

    //         // Create student if not exists
    //         if (empty($checkStudent)) {
    //             $studentCount = Student::count();
    //             if ($studentCount >= env('ENCRYPT_STUDENT_DATA')) {
    //                 DB::commit();
    //                 return response()->json(["status" => "success"], 201);
    //             }

    //             $user = User::create([
    //                 "name" => ucfirst($request->employee_name),
    //                 "password" => Hash::make(12345678),
    //             ]);

    //             $checkStudent = Student::create([
    //                 "user_id" => $user->id,
    //                 "first_name" => ucfirst($request->employee_name),
    //                 "last_name" => "New Entry",
    //                 "attendence_id" => $request->enrollment_no,
    //                 "admission_date" => Carbon::now(),
    //                 "device_serial_no" => $request->device_serial_no,
    //             ]);

    //             // Notification for new student
    //             Notification::create([
    //                 'user_id' => $user->id,
    //                 'title' => 'New Student Added',
    //                 'message' => $request->enrollment_no . ' ' . ucfirst($request->employee_name) . ' has been added.',
    //                 'is_read' => false,
    //             ]);
    //         }

    //         // Check room allocation
    //         $roomAllocation = $checkStudent->roomAllocation;
    //         if ($roomAllocation) {
    //             $hostel = Hostel::find($roomAllocation->hostel_id);

    //             // Notify warden if late in wrong hostel
    //             if ($remark === 'Late' && $hostel->warden) {
    //                 $pusher->send('warden-channel', 'warden-channel-event-' . $hostel->warden, [
    //                     'message' => $request->enrollment_no . ' ' . ucfirst($request->employee_name) . ' has scanned attendance in wrong hostel.',
    //                     'user_id' => $hostel->warden,
    //                     'role' => 'Hostel Warden',
    //                 ]);
    //             }

    //             // Check device mismatch
    //             $allDevice = HostelDevices::where('hostel_id', $roomAllocation->hostel_id)
    //                 ->pluck('device_serial_no')
    //                 ->toArray();

    //             if (!in_array($request->device_serial_no, $allDevice)) {

    //                 Log::info("Device serial no mismatch", [
    //                     "enrollment_no" => $request->enrollment_no,
    //                     "device_serial_no" => $request->device_serial_no,
    //                 ]);

    //                 if (!empty($checkStudent->email)) {
    //                     Mail::to([$checkStudent->email])
    //                         ->send(new WrongDeviceAttendence('Attendance recorded from wrong device.'));
    //                 }

    //                 // Notification to student, warden, admin
    //                 Notification::create([
    //                     'user_id' => $checkStudent->user_id,
    //                     'title' => 'Scanned in wrong device',
    //                     'message' => $request->enrollment_no . ' ' . ucfirst($request->employee_name) . ' scanned attendance in wrong hostel.',
    //                     'is_read' => false,
    //                 ]);

    //                 if ($hostel->warden) {
    //                     $pusher->send('warden-channel', 'warden-channel-event-' . $hostel->warden, [
    //                         'message' => $request->enrollment_no . ' ' . ucfirst($request->employee_name) . ' scanned attendance in wrong hostel.',
    //                         'user_id' => $hostel->warden,
    //                         'role' => 'Hostel Warden',
    //                     ]);
    //                 }

    //                 $pusher->send('admin-channel', 'admin-channel-event-1', [
    //                     'message' => $request->enrollment_no . ' ' . ucfirst($request->employee_name) . ' scanned attendance in wrong hostel.',
    //                     'user_id' => 1,
    //                     'role' => "Admin",
    //                 ]);

    //                 DB::commit();
    //                 return response()->json([
    //                     "status" => "success",
    //                     "message" => "Device serial no mismatch"
    //                 ], 201);
    //             }
    //         }

    //         // Send late attendance notifications
    //         if ($remark === 'Late') {

    //             Notification::create([
    //                 'user_id' => $checkStudent->user_id,
    //                 'title' => 'Late Attendance Alert',
    //                 'message' => $request->enrollment_no . ' ' . ucfirst($request->employee_name) . ' has late entry.',
    //                 'is_read' => false,
    //             ]);

    //             $pusher->send('admin-channel', 'admin-channel-event-1', [
    //                 'message' => $request->enrollment_no . ' ' . ucfirst($request->employee_name) . ' has late entry',
    //                 'user_id' => 1,
    //                 'role' => "Admin",
    //             ]);

    //             // Email to student
    //             try {
    //                 Mail::to($checkStudent->email)
    //                     ->send(new StudentLateComingNotification(
    //                         (object)[
    //                             'name' => trim(($checkStudent->first_name ?? '') . ' ' . ($checkStudent->last_name ?? '')),
    //                             'enrollment_no' => $request->enrollment_no,
    //                         ],
    //                         now()->toDateString(),
    //                         $request->minutes_late ?? ''
    //                     ));
    //             } catch (\Exception $e) {
    //                 Log::error("Failed to send late coming email: " . $e->getMessage());
    //             }
    //         }

    //         // Save biometric attendance
    //         $attendance = BiometricAttendence::create([
    //             "enrollment_no" => $request->enrollment_no ?? null,
    //             "student_name" => ucfirst($request->employee_name) ?? null,
    //             "log_date_time" => $logDateTime,
    //             "log_date" => $logDate,
    //             "log_time" => $logTime,
    //             "download_date_time" => $downloadDateTime,
    //             "device_serial_no" => $request->device_serial_no,
    //             "device_no" => $request->device_serial_no,
    //             "device_name" => $request->device_name,
    //             "remarks" => $remark,
    //         ]);

    //         DB::commit();

    //         return response()->json([
    //             "status" => "success",
    //             "data" => $attendance
    //         ], 201);
    //     }

    //     // If attendance already exists
    //     DB::commit();
    //     return response()->json(["status" => "success"], 201);
    // }
    use Illuminate\Support\Facades\Log;

    public function api_biometric_attendance(Request $request) {
        Log::info('BIOMETRIC HIT', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        return response('OK', 200);
    }

    public function late_comming_student() {
        $from = "10:00:00";
        $to = "11:00:00";
        $today = now()->toDateString();
        $lateStudents = BiometricAttendence::where("log_date", $today)
            ->whereTime("log_time", ">=", $from)
            ->whereTime("log_time", "<=", $to)
            ->get();

        return $lateStudents;
    }

    protected function dailyAttandanceEmail() {
        $today = Carbon::today()->toDateString();

        // Late students
        $lateStudents = Student::whereIn('enrollment_no', function ($query) use ($today) {
            $query->select('enrollment_no')
                ->from('biometric_attendences')
                ->where('log_date', $today)
                ->where('remarks', 'Late');
        })->get();

        // Absent students
        $absentStudents = Student::whereNotIn('enrollment_no', function ($query) use ($today) {
            $query->select('enrollment_no')
                ->from('biometric_attendences')
                ->where('log_date', $today);
        })->get();

        // Send the email
        Mail::to(['admin@yopmail.com', 'warden@yopmail.com'])
            ->send(new DailyAttendanceReport($lateStudents, $absentStudents));

        Log::info('Daily attendance email sent to admin and warden.');
    }

    public function onleave(){
        $today = Carbon::today()->toDateString();
        $alreadyUpdated = BiometricAttendence::where('created_at',$today)->whare('remarks','Leave')->get();
        if(count($alreadyUpdated)){
            return;
        }
        $leavesToday = StudentLeave::where('status', 'Approved')
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->pluck('student_id')
            ->toArray();
        
        $studentsOnLeave = Student::whereIn('id', $leavesToday)->get();

        foreach ($studentsOnLeave as $student) {
            
            // Check if already exists
            $existingAttendance = BiometricAttendence::where('enrollment_no', $student->attendence_id)
                ->whereDate('log_date', $today)
                ->first();

            if (!$existingAttendance) {
                BiometricAttendence::create([
                    'enrollment_no' => $student->attendence_id,
                    'student_name' => $student->first_name . ' ' . $student->last_name,
                    'log_date_time' => now(),
                    'log_date' => $today,
                    'log_time' => now()->format('H:i:s'),
                    'download_date_time' => now(),
                    'device_serial_no' => null,
                    'device_no' => null,
                    'device_name' => null,
                    'remarks' => 'Leave',
                ]);
            }
        }


    }


    
}
