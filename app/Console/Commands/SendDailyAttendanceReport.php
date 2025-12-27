<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Student;
use App\Models\StudentLeave;

use App\Mail\DailyAttendanceReport;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendDailyAttendanceReport extends Command
{
    protected $signature = 'report:daily-attendance';
    protected $description = 'Send daily attendance report email';

    public function handle()
    {
        $today = Carbon::now('Asia/Kolkata')->toDateString();
        $to = "11:00:00";

        $lateStudents = Student::whereHas('biometricAttendances', function ($query) use ($to, $today) {
            $query->whereDate('log_date', $today)
                ->where('log_time', '>', $to);
        })->get();

        // Students on approved leave today
        $leavesToday = StudentLeave::where('status', 'Approved')
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->pluck('student_id')
            ->toArray();

        // Absent students (excluding students on leave)
        $absentStudents = Student::whereNotIn('id', $leavesToday) // exclude leave students
            ->whereNotIn('enrollment_no', function ($query) use ($today) {
                $query->select('enrollment_no')
                    ->from('biometric_attendances')
                    ->whereDate('log_date', $today);
            })
            ->get();

        Log::info("SendDailyAttendanceReport triggered for date: {$today}");
        Log::info("Late students count: " . $lateStudents->count());
        Log::info("Absent students count: " . $absentStudents->count());

        try {
            Mail::to(['shailender.digittrix@gmail.com', 'warden@yopmail.com'])
                ->send(new DailyAttendanceReport($lateStudents, $absentStudents));

            $this->info('Daily attendance report sent.');
            Log::info('Daily attendance report email sent successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to send daily attendance report: ' . $e->getMessage());
            Log::error('Failed to send daily attendance report: ' . $e->getMessage());
        }
    }
}
