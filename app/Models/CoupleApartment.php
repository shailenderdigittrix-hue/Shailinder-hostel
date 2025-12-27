<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoupleApartment extends Model
{
      protected $table = 'couple_apartment';
      protected $fillable = [
        'apartment_number',
        'name',
        'type',
        'description',
        'floor_number',
        'total_floors',
        'bedrooms',
        'bathrooms',
        'balconies',
        'furnished_status',
        'parking_available',
        'mess_id',
    ];

    public function apartmentMember() {
        return $this->hasMany(ApartmentMember::class, 'apartment_id', 'id');
    }
}
