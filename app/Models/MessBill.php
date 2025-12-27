<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessBill extends Model
{
    protected $fillable = ['student_id', 'month', 'days', 'amount', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    
}
