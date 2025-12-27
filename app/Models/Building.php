<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = [
        'hostel_id',
        'name',
        'number_of_floors',
        'image'
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }



}
