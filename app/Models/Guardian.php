<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = [
        'student_id', 
        'name', 
        'phone',
        'email', 
        'relationship',
        'status', 
    ];


}
