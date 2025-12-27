<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'current_room_id', 'desired_room_id', 'reason',
        'status', 'approved_at', 'processed_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function currentRoom()
    {
        return $this->belongsTo(Room::class, 'current_room_id');
    }

    public function desiredRoom()
    {
        return $this->belongsTo(Room::class, 'desired_room_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
