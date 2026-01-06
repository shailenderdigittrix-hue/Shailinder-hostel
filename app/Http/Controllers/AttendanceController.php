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
    // use Illuminate\Support\Facades\Log;

    // public function api_biometric_attendance(Request $request) {
    //     $requestCode = $request->header('request-code');

    //     Log::info('BIOMETRIC HIT', [
    //         'request_code' => $requestCode,
    //         'headers' => $request->headers->all(),
    //         'body' => $request->getContent(), // raw body (important)
    //     ]);

    //     // 1️⃣ Device asking for commands
    //     if ($requestCode === 'receive_cmd') {
    //         // Tell device: no command
    //         return response('', 200)
    //             ->header('response_code', 'OK');
    //     }

    //     // 2️⃣ REAL-TIME ATTENDANCE EVENT
    //     if ($requestCode === 'realtime_glog') {

    //         $json = json_decode($request->getContent(), true);

    //         Log::info('ATTENDANCE RECEIVED', $json);

    //         // Example: save attendance here later

    //         return response('', 200)
    //             ->header('response_code', 'OK');
    //     }

    //     return response('', 200);
    // }

    // public function api_biometric_attendance(Request $request) {
    //     $requestCode = $request->header('request-code');

    //     // 🔹 Read RAW octet-stream body
    //     $rawBody = file_get_contents('php://input');

    //     // 🔹 Remove first 4 bytes (binary header)
    //     $jsonBody = strlen($rawBody) > 4 ? substr($rawBody, 4) : null;

    //     // 🔹 Decode JSON safely
    //     $data = $jsonBody ? json_decode($jsonBody, true) : null;

    //     Log::info('BIOMETRIC HIT', [
    //         'request_code' => $requestCode,
    //         'device_serial_no' => $request->header('dev-id'),
    //         'transaction_id' => $request->header('trans-id'),
    //         'raw_length' => strlen($rawBody),
    //         'parsed_body' => $data,
    //     ]);

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 1️⃣ DEVICE HEARTBEAT / COMMAND REQUEST
    //     |--------------------------------------------------------------------------
    //     | request_code = receive_cmd
    //     | This is NOT attendance data
    //     */
    //     if ($requestCode === 'receive_cmd') {

    //         // Device info available here
    //         // fk_name, firmware, supported_enroll_data, etc.

    //         return response('', 200)
    //             ->header('response_code', 'OK');
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 2️⃣ REAL-TIME ATTENDANCE LOG
    //     |--------------------------------------------------------------------------
    //     | request_code = realtime_glog
    //     */
    //     if ($requestCode === 'realtime_glog' && $data) {

    //         // Example expected fields (depends on device config)
    //         // enrollment_no / user_id
    //         // verify_type
    //         // fk_time / log_time

    //         $dt = null;
    //         if (!empty($data['fk_time'])) {
    //             $dt = \Carbon\Carbon::createFromFormat('YmdHis', $data['fk_time']);
    //         }

    //         // $attendance = [
    //         //     'enrollment_no'       => $data['enroll_id'] ?? null,
    //         //     'employee_name'       => $data['name'] ?? null,
    //         //     'device_name'         => $data['fk_name'] ?? null,
    //         //     'device_serial_no'    => $request->header('dev-id'),
    //         //     'device_no'           => $request->header('trans-id'),
    //         //     'log_date_time'       => $dt ? $dt->format('Y-m-d H:i:s') : null,
    //         //     'log_date'            => $dt ? $dt->format('Y-m-d') : null,
    //         //     'log_time'            => $dt ? $dt->format('H:i:s') : null,
    //         //     'download_date_time'  => now()->format('Y-m-d H:i:s'),
    //         // ];

    //         Log::info('ATTENDANCE PARSED', $attendance);

    //         // 🔹 Save to DB here later
    //         // Attendance::create($attendance);

    //         return response('', 200)
    //             ->header('response_code', 'OK');
    //     }

    //     return response('', 200)
    //         ->header('response_code', 'OK');
    // }

    // public function api_biometric_attendance(Request $request)
    // {
    //     // 1️⃣ Log normal request params (mostly empty for biometric)
    //     file_put_contents(
    //         storage_path('logs/request.txt'),
    //         "\n----- REQUEST PARAMS -----\n" . print_r($_REQUEST, true),
    //         FILE_APPEND
    //     );

    //     // 2️⃣ Log raw binary payload (IMPORTANT)
    //     $raw = file_get_contents('php://input');

    //     file_put_contents(
    //         storage_path('logs/request.txt'),
    //         "\n----- RAW PAYLOAD (" . strlen($raw) . ") -----\n",
    //         FILE_APPEND
    //     );

    //     file_put_contents(
    //         storage_path('logs/request.txt'),
    //         bin2hex($raw) . "\n",
    //         FILE_APPEND
    //     );

    //     // 3️⃣ ACK device
    //     return response('', 200)
    //         ->header('Content-Type', 'application/octet-stream')
    //         ->header('response_code', 'OK')
    //         ->header('Content-Length', '0');
    // }

    // public function api_biometric_attendance(Request $request) {
    //     $raw = file_get_contents('php://input');
    //     $len = strlen($raw);

    //     $requestCode = $request->header('request_code');
    //     $deviceId    = $request->header('dev_id');

    //     Log::info('BIOMETRIC HIT', [
    //         'request_code' => $requestCode,
    //         'device_id'    => $deviceId,
    //         'raw_length'   => $len
    //     ]);

    //     if ($requestCode === 'realtime_glog') {
    //         return $this->handleRealtimeGlog($raw, $deviceId);
    //     }

    //     return response('', 200)
    //         ->header('Content-Type', 'application/octet-stream')
    //         ->header('response_code', 'OK')
    //         ->header('Content-Length', '0');
    // }

    // private function handleRealtimeGlog(string $raw, string $deviceId) {
    //     /**
    //      * Step 1: Extract JSON (it always comes first)
    //      */
    //     $jsonEndPos = strpos($raw, '}');
    //     if ($jsonEndPos === false) {
    //         Log::error('Invalid realtime_glog payload');
    //         return $this->ackError();
    //     }

    //     $jsonString = substr($raw, 0, $jsonEndPos + 1);
    //     $binaryData = substr($raw, $jsonEndPos + 1);

    //     $data = json_decode($jsonString, true);

    //     if (!$data) {
    //         Log::error('JSON decode failed', ['json' => $jsonString]);
    //         return $this->ackError();
    //     }

    //     Log::info('REALTIME GLOG JSON', $data);
    //     Log::info('REALTIME GLOG BINARY LEN', ['len' => strlen($binaryData)]);

    //     /**
    //      * Step 2: Save attendance
    //      */
    //     $attendanceTime = \Carbon\Carbon::createFromFormat(
    //         'YmdHis',
    //         $data['io_time']
    //     );

    //     DB::table('attendance_logs')->insert([
    //         'device_id'   => $deviceId,
    //         'user_id'     => $data['user_id'],
    //         'verify_mode' => json_encode($data['verify_mode']),
    //         'io_mode'     => $data['io_mode'],
    //         'io_time'     => $attendanceTime,
    //         'created_at'  => now()
    //     ]);

    //     /**
    //      * Step 3: Save image if exists
    //      */
    //     if (!empty($binaryData)) {
    //         Storage::put(
    //             'biometric_images/' . uniqid() . '.jpg',
    //             $binaryData
    //         );
    //     }

    //     return $this->ackSuccess();
    // }

    

    // private function handleRealtimeGlog(string $raw, string $deviceId) {
    //     // 1️⃣ Split JSON and binary
    //     $jsonEndPos = strpos($raw, '}');
    //     if ($jsonEndPos === false) {
    //         Log::error('BIOMETRIC GLOG INVALID PAYLOAD', [
    //             'device_id' => $deviceId,
    //             'raw_len'   => strlen($raw)
    //         ]);
    //         return $this->ackError();
    //     }

    //     $jsonString = substr($raw, 0, $jsonEndPos + 1);
    //     $binaryData = substr($raw, $jsonEndPos + 1);

    //     $data = json_decode($jsonString, true);

    //     if (!$data) {
    //         Log::error('BIOMETRIC GLOG JSON DECODE FAILED', [
    //             'device_id' => $deviceId,
    //             'json'      => $jsonString
    //         ]);
    //         return $this->ackError();
    //     }

    //     // 2️⃣ Convert time (do NOT store)
    //     $attendanceTime = null;
    //     try {
    //         $attendanceTime = \Carbon\Carbon::createFromFormat(
    //             'YmdHis',
    //             $data['io_time']
    //         )->toDateTimeString();
    //     } catch (\Exception $e) {
    //         Log::warning('BIOMETRIC TIME PARSE FAILED', [
    //             'device_id' => $deviceId,
    //             'io_time'   => $data['io_time']
    //         ]);
    //     }

    //     // 3️⃣ LOG EVERYTHING (NO DB, NO FILES)
    //     Log::info('BIOMETRIC REALTIME GLOG RECEIVED', [
    //         'device_id'     => $deviceId,
    //         'user_id'       => $data['user_id'] ?? null,
    //         'verify_mode'   => $data['verify_mode'] ?? null,
    //         'io_mode'       => $data['io_mode'] ?? null,
    //         'io_time_raw'   => $data['io_time'] ?? null,
    //         'io_time_parsed'=> $attendanceTime,
    //         'binary_len'    => strlen($binaryData),
    //         'binary_sha1'   => $binaryData ? sha1($binaryData) : null
    //     ]);

    //     return $this->ackSuccess();
    // }


    // private function ackSuccess() {
    //     return response('', 200)
    //         ->header('Content-Type', 'application/octet-stream')
    //         ->header('response_code', 'OK')
    //         ->header('Content-Length', '0');
    // }

    // private function ackError() {
    //     return response('', 200)
    //         ->header('Content-Type', 'application/octet-stream')
    //         ->header('response_code', 'ERROR')
    //         ->header('Content-Length', '0');
    // }



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


    





    // This is working fine -----------------------
    // public function api_biometric_attendance(Request $request) {
    //     // 🔹 1. READ HEADERS (IMPORTANT)
    //     $requestCode = $request->header('request_code');
    //     $deviceId    = $request->header('dev_id');

    //     // 🔹 2. READ RAW BODY (binary + json)
    //     $raw = $request->getContent();

    //     Log::info('BIOMETRIC HIT', [
    //         'request_code' => $requestCode,
    //         'device_id'    => $deviceId,
    //         'raw_length'   => strlen($raw),
    //     ]);

    //     // 🔹 3. STRIP FIRST 4 BYTES (binary length header)
    //     if (strlen($raw) <= 4) {
    //         Log::error('BIOMETRIC BODY TOO SHORT', ['device_id' => $deviceId]);
    //         return response('OK', 200);
    //     }

    //     $jsonString = substr($raw, 4);

    //     // 🔹 4. DECODE JSON
    //     $data = json_decode($jsonString, true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         Log::error('BIOMETRIC GLOG JSON DECODE FAILED', [
    //             'device_id' => $deviceId,
    //             'json'      => $jsonString,
    //             'error'     => json_last_error_msg(),
    //         ]);
    //         return response('OK', 200);
    //     }

    //     // 🔹 5. LOG ONLY (NO DB)
    //     Log::info('BIOMETRIC ATTENDANCE DATA', [
    //         'device_id'   => $deviceId,
    //         'user_id'     => $data['user_id'] ?? null,
    //         'verify_mode' => $data['verify_mode'] ?? null,
    //         'io_mode'     => $data['io_mode'] ?? null,
    //         'io_time'     => $data['io_time'] ?? null,
    //     ]);

    //     // 🔹 6. RESPONSE REQUIRED BY DEVICE
    //     return response('OK', 200)
    //         ->header('response_code', 'OK')
    //         ->header('Content-Type', 'application/octet-stream')
    //         ->header('Content-Length', '0');
    // }

    
    // public function api_biometric_attendance(Request $request) {
    //     $raw = file_get_contents('php://input');
    //     Log::info('Request Headers:', $request->headers->all());
    //     $requestCode = $request->header('request_code'); // e.g., "receive_cmd"
    //     $deviceId = $request->header('dev_id');           // e.g., "RSS20241198692"
        
    //     // Skip the first 4 bytes (if needed) and get the remaining data
    //     $json = substr($raw, 4);
        
    //     // Decode the JSON data into an associative array
    //     $data = json_decode($json, true);
        
    //     // If JSON decoding fails, log the error and stop
    //     if (!$data) {
    //         Log::error('GLOG JSON FAILED', [
    //             'device_id' => $deviceId,
    //             'raw' => bin2hex(substr($raw, 0, 20)) // Log the first 20 bytes of raw data
    //         ]);
    //         return response()->json(['error' => 'Invalid JSON format'], 400);
    //     } 
    //     else {
    //         // Log successful JSON parsing
    //         Log::info('GLOG JSON SUCCESS', [
    //             'device_id' => $deviceId,
    //             'data' => $data['fk_info'], // Log the decoded data
    //         ]);
    //     }

    //     // Check if we have the expected attendance log data
    //     if (isset($data['attendance_log']) && is_array($data['attendance_log'])) {
    //         foreach ($data['attendance_log'] as $attendance) {
    //             // Log each attendance entry for debugging
    //             Log::info('Attendance Log:', $attendance);

    //             // Check if it's a face scan (verify_mode: 10 indicates a face scan in this example)
    //             if (isset($attendance['verify_mode']) && $attendance['verify_mode'] == 10) {
    //                 // This is a face scan
    //                 Log::info('Face scan detected for user:', [
    //                     'user_id' => $attendance['user_id'],
    //                     'verify_mode' => $attendance['verify_mode'],
    //                     'io_time' => $attendance['io_time']
    //                 ]);

    //                 // Do something with the face scan data, e.g., mark attendance
    //                 // This can be custom logic for updating a database or triggering other processes
    //                 // For example, you can save attendance or validate user information here
    //             }
    //         }
    //     } else {
    //         Log::warning('No attendance log found in the data.', [
    //             'device_id' => $deviceId
    //         ]);
    //     }
        
    //     // Return a response to acknowledge receipt of the data
    //     return response()->json(['status' => 'success'], 200);



    //     // Log::info('BIOMETRIC HIT', [
    //     //     'request_code' => $requestCode,
    //     //     'device_id' => $deviceId,
    //     //     'raw_length' => strlen($raw),
    //     // ]);

    //     // if ($requestCode === 'receive_cmd') {
    //     //     return response('', 200)
    //     //         ->header('response_code', 'OK')
    //     //         ->header('Content-Length', '0');
    //     // }

    //     // $handleAttendance = null;
    //     // if ($requestCode === 'realtime_glog') {
    //     //     $handleAttendance = $this->handleAttendance($deviceId, $raw);
    //     //     return response('', 200)->header('response_code', 'OK');
    //     // }

    //     // if ($requestCode === 'realtime_enroll_data') {
    //     //     $this->handleEnroll($deviceId, $raw);
    //     //     return response('', 200)->header('response_code', 'OK');
    //     // }
    // }

        // ------ OLD CODE FINISH --------

    //     return response('', 200)->header('response_code', 'OK');
    // }

    private function handleAttendance($deviceId, $raw) {
        // remove 4-byte binary prefix
        $json = substr($raw, 4);

        $data = json_decode($json, true);

        if (!$data) {
            Log::error('GLOG JSON FAILED', [
                'device_id' => $deviceId,
                'raw' => bin2hex(substr($raw, 0, 20))
            ]);
            return;
        }

        Log::info('ATTENDANCE LOG', [
            'device_id'   => $deviceId,
            'user_id'     => $data['user_id'] ?? null,
            'verify_mode' => $data['verify_mode'] ?? null,
            'io_mode'     => $data['io_mode'] ?? null,
            'io_time'     => $data['io_time'] ?? null,
        ]);

        // $handleAttendance = $data ;
        
        // // if (!$handleAttendance) {
        // //     Log::warning("No attendance data processed for request_code: $requestCode");
        // //     return response()->json(["status" => "no_data"], 400);
        // // }

        // // Parse date/time safely
        // try {
        //     $logDateTime = Carbon::createFromFormat('YmdHis', $handleAttendance->io_time);
        // } catch (\Exception $e) {
        //     Log::error("Invalid io_time format: " . $handleAttendance->io_time);
        //     return response()->json(["status" => "invalid_time"], 400);
        // }


        // // ------ OLD CODE HERE ----------
        // $logTime = date("H:i:s", strtotime($logDateTime));

        // // Determine remark
        // $from = "10:00:00";
        // $to = "11:00:00";
        // if ($logTime >= $from && $logTime <= $to) {
        //     $remark = "Present";
        // } elseif ($logTime > $to) {
        //     $remark = "Late";
        // } else {
        //     $remark = "Absent";
        // }
        // Log::info("Biometric remark", ['remark' => $remark]);
        
        // DB::beginTransaction();
        // try {
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
        //         ->where("enrollment_no", $handleAttendance->enrollment_no)
        //         ->where("device_serial_no", $handleAttendance->device_id)
        //         ->first();

        //     if (empty($checkAttendanceToday)) {
        //         // Check if student exists
        //         $checkStudent = Student::with('roomAllocation')
        //             ->where("attendence_id", $handleAttendance->enrollment_no)
        //             ->where("device_serial_no", $handleAttendance->device_id)
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
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error("Error saving biometric attendance: " . $e->getMessage());
        //     return response()->json(["status" => "error", "message" => $e->getMessage()], 500);
        // }
    }

    private function handleEnroll($deviceId, $raw) {
        // JSON always starts after 4 bytes
        $json = substr($raw, 4);
        $data = json_decode($json, true);

        if (!$data) {
            Log::error('ENROLL JSON FAILED', ['device_id' => $deviceId]);
            return;
        }

        Log::info('ENROLL DATA', [
            'device_id' => $deviceId,
            'user_id'   => $data['user_id'] ?? null,
            'name'      => $data['user_name'] ?? null,
            'level'     => $data['user_privilege'] ?? null,
            'parts'     => count($data['enroll_data_array'] ?? [])
        ]);
    }


    // public function api_biometric_attendance(Request $request) {
    //     // Get raw input data from the request body
    //     $raw = file_get_contents('php://input');
        
    //     // Log the incoming headers for traceability (useful for debugging)
    //     Log::info('Request Headers:', $request->headers->all());

    //     // Extract specific headers that are needed for validation (e.g., "request_code" and "dev_id")
    //     $requestCode = $request->header('request_code');
    //     $deviceId = $request->header('dev_id');

    //     // Validate that both headers exist before continuing
    //     if (empty($requestCode) || empty($deviceId)) {
    //         Log::error('Missing required headers', [
    //             'request_code' => $requestCode,
    //             'dev_id' => $deviceId
    //         ]);
    //         return response()->json([
    //             'error' => 'Missing required headers (request_code, dev_id)'
    //         ], 400);
    //     }

    //     // Log the incoming request code and device ID for traceability
    //     Log::info('Device Info:', ['request_code' => $requestCode, 'dev_id' => $deviceId]);

    //     // Skip the first 4 bytes (if required by the machine's protocol) and get the remaining data
    //     $json = substr($raw, 4);
        
    //     // Decode the JSON data into an associative array
    //     $data = json_decode($json, true);

    //     // Check if JSON decoding was successful
    //     if (!$data) {
    //         Log::error('JSON Decoding Failed', [
    //             'device_id' => $deviceId,
    //             'raw_data' => bin2hex(substr($raw, 0, 20)) // Log the first 20 bytes of raw data for debugging
    //         ]);
    //         return response()->json([
    //             'error' => 'Invalid JSON format',
    //             'raw_data' => bin2hex(substr($raw, 0, 20)) // Provide raw data for debugging
    //         ], 400);
    //     }

    //     // Log successful JSON parsing
    //     Log::info('JSON Decoding Success', [
    //         'device_id' => $deviceId,
    //         'fk_info' => $data['fk_info'] ?? 'Not available', // Safely access 'fk_info'
    //     ]);

    //     // Validate that the parsed data contains required fields (e.g., fk_info)
    //     if (!isset($data['fk_info'])) {
    //         Log::error('Missing fk_info in the data', [
    //             'device_id' => $deviceId,
    //             'data' => $data
    //         ]);
    //         return response()->json([
    //             'error' => 'Missing fk_info in the parsed data'
    //         ], 400);
    //     }

    //     // At this point, we have valid JSON data and can proceed with further processing
    //     // For example, you could save the data to the database, etc.

    //     // For now, we return a success message, including the parsed data for debugging
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Biometric attendance data processed successfully',
    //         'data' => $data // Return the parsed data for debugging purposes
    //     ]);
    // }

    public function api_biometric_attendance(Request $request) {
        // print_r($request->all());
                    Log::info('Raw request body--22:', ['request_body' => $request->all()]);
        // Log the entire request body to inspect the content
        Log::info('Raw request body:', ['request_body' => $request->getContent()]);

        // Get raw data from the request body
        $raw = file_get_contents('php://input');
        Log::info('Raw data received:', ['data' => $raw]);

        // Decode the JSON payload to get the 'data' field (hex-encoded)
        $json_data = json_decode($raw, true);
        $hex_data = $json_data['data'] ?? '';  // Ensure 'data' key is used
        
        if (empty($hex_data)) {
            Log::error('No "data" found in the request');
            return response()->json(['error' => 'No "data" field in the request'], 400);
        }

        // Decode the hexadecimal data to binary
        $binary_data = hex2bin($hex_data);

        // Now decode the binary data to JSON (assuming the binary data is a JSON string)
        $decoded_data = json_decode($binary_data, true);

        // Log the decoded data for debugging purposes
        Log::info('Decoded data:', ['decoded_data' => $decoded_data]);

        // Check if required fields are present in the decoded data
        if (isset($decoded_data['employee_id'], $decoded_data['timestamp'], $decoded_data['status'])) {
            // Save attendance data to the database
            $attendance = new Attendance();
            $attendance->employee_id = $decoded_data['employee_id'];
            $attendance->timestamp = $decoded_data['timestamp'];
            $attendance->status = $decoded_data['status'];
            $attendance->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance data saved successfully',
            ]);
        } else {
            Log::error('Missing required fields', ['data' => $decoded_data]);
            return response()->json([
                'error' => 'Invalid or missing attendance data',
                'decoded_data' => $decoded_data // Return the decoded data for debugging
            ], 400);
        }
    }







}
