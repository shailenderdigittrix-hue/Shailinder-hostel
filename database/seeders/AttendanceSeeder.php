<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $attendances = [
            [
                'student_id' => 1,
                'enrollment_id' => 'B10682',
                'attendance_date' => '2025-10-01',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 2,
                'enrollment_id' => 'B60695',
                'attendance_date' => '2025-10-02',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 3,
                'enrollment_id' => 'B81430',
                'attendance_date' => '2025-10-02',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],

             [
                'student_id' => 1,
                'enrollment_no' => 'B10682',
                'attendance_date' => '2025-10-03',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'student_id' => 2,
                'enrollment_id' => 'B60695',
                'attendance_date' => '2025-10-03',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'student_id' => 3,
                'enrollment_id' => 'B81430',
                'attendance_date' => '2025-10-03',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 1,
                'enrollment_id' => 'B10682',
                'attendance_date' => '2025-10-04',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 2,
                'enrollment_id' => 'B60695',
                'attendance_date' => '2025-10-04',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 3,
                'enrollment_id' => 'B81430',
                'attendance_date' => '2025-10-04',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'student_id' => 1,
                'enrollment_no' => 'B10682',
                'attendance_date' => '2025-10-05',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 2,
                'enrollment_id' => 'B60695',
                'attendance_date' => '2025-10-05',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 3,
                'enrollment_id' => 'B81430',
                'attendance_date' => '2025-10-05',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],

             [
                'student_id' => 1,
                'enrollment_id' => 'B10682',
                'attendance_date' => '2025-10-06',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 2,
                'enrollment_id' => 'B60695',
                'attendance_date' => '2025-10-06',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 3,
                'enrollment_id' => 'B81430',
                'attendance_date' => '2025-10-6',
                'check_in' => '09:05:00',
                'check_out' => '16:55:00',
                'status' => 'Present',
                'remarks' => 'On time',
                'created_at' => now(),
                'updated_at' => now(),
            ],


         
          
        ];

        DB::table('student_attendances')->insert($attendances);
    }
}
