<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAllocation extends Model
{
    protected $fillable = [
        'student_id',
        'hostel_id',
        'building_id',
        'floor',
        'room_id',
        'allocated_at',
        'deallocated_at',
    ];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function currentRoomAllocation()
    {
        return $this->hasOne(RoomAllocation::class)->whereNull('deallocated_at');
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }



    
    
}
