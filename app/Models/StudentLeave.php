<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentLeave extends Model
{
    protected $fillable = [
        'student_id',
        'from_date',
        'to_date',
        'reason',
        'remarks',
        'status',
        'document',
    ];

    /**
     * Relationship: This leave belongs to a student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
