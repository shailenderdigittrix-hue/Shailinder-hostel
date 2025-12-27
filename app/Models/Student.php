<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'enrollment_no',
        'attendence_id',
        'gender',
        'device_serial_no',
        'date_of_birth',
        'email',
        'phone',
        'course_id',
        'year',
        'address',
        'admission_date',
        'feeCategory',
        'profile_image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'device_serial_no', 'device_serial_no');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function roomAllocation()
    {
        // One active allocation per student
        return $this->hasOne(RoomAllocation::class)->whereNull('deallocated_at');
    }

    public function room()
    {
        // Access the currently allocated room via roomAllocation
        return $this->hasOneThrough(
            Room::class,          // Final model
            RoomAllocation::class,// Intermediate model
            'student_id',         // Foreign key on room_allocations table
            'id',                 // Foreign key on rooms table
            'id',                 // Local key on students table
            'room_id'             // Local key on room_allocations table
        )->whereNull('room_allocations.deallocated_at');
    }

    public function currentRoomAllocation(){
        return $this->hasOne(RoomAllocation::class)->whereNull('deallocated_at');
    }

    public function biometricAttendances()
    {
        return $this->hasMany(BiometricAttendence::class, 'enrollment_no', 'attendence_id');
    }

    public function mess()
    {
        return $this->belongsTo(Mess::class);
    }

    public function messBills()
    {
        return $this->hasMany(MessBill::class);
    }

    public function violations() {
        return $this->hasMany(DisciplinaryViolation::class);
    }
    
    // public function fines()
    // {
    //     return $this->hasMany(Fine::class);  // or whichever model/table
    // }

    // public function disciplinaryActions()
    // {
    //     return $this->hasMany(DisciplinaryAction::class);
    // }


}
