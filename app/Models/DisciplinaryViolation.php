<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplinaryViolation extends Model
{
    protected $fillable = [
        'student_id',
        'violation_date',
        'type', // if using string
        'details',
        'status',
        'fine_amount',
        'fine_reason',
        'fine_issued_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'fine_issued_at' => 'date',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Optional: if using a separate ViolationType table
    public function violationType()
    {
        return $this->belongsTo(ViolationType::class, 'type');
    }

    


}

