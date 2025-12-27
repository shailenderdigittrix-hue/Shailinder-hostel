<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\Student;

class BiometricAttendence extends Model
{
    protected $fillable = [
        'enrollment_no',
        'student_name',
        'log_date_time',
        'log_date',
        'log_time',
        'download_date_time',
        'device_serial_no',
        'device_no',
        'device_name',
        'remarks'
    ];

    public function student() {
        return $this->belongsTo(Student::class, 'enrollment_no', 'attendence_id');
    }
}

