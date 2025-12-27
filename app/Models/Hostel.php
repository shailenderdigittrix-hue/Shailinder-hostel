<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $fillable = [
        'name',
        'code',
        'gender',
        'device_serial_no',
        'building',
        'total_capacity',
        'warden',
        'contact',
        'email',
        'address',
        'facilities',
        'is_active',
    ];

    protected $casts = [
        'facilities' => 'array',
        'is_active' => 'boolean',
    ];

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }
    
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function hostelDevices()
    {
        return $this->hasMany(HostelDevices::class, 'hostel_id', 'id');
    }

}
