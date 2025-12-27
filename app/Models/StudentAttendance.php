<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $table = 'student_attendances';

    protected $fillable = [
        'student_id',
        'enrollment_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'remarks',
    ];

    // Relationship to Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    // public function student()
    // {
    //     return $this->belongsTo(Student::class);
    // }

}
