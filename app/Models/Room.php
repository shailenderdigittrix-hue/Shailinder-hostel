<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'hostel_id',
        'building_id',
        'floor',
        'room_number',
        'capacity',
        'room_type',
        'is_active',
    ];

    public function hostel(){
        return $this->belongsTo(Hostel::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function students() {
        return $this->hasMany(Student::class);
    }

    public function allocations()
    {
        return $this->hasMany(RoomAllocation::class);
    }

    public function activeAllocations()
    {
        return $this->hasMany(RoomAllocation::class)->whereNull('deallocated_at');
    }



    
    
        
}
