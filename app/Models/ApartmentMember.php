<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentMember extends Model
{
       protected $table = 'apartment_members';
       protected $fillable = [
        'apartment_id',
        'name',
        'relation',
        'age'
      ];

      public function coupleApartment() {
         return $this->belongsTo(CoupleApartment::class, 'apartment_id', 'id');
      }
}
