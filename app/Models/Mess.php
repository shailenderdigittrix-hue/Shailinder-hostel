<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mess extends Model
{
    use HasFactory;

    protected $table = 'mess';

    protected $fillable = [
        'name',
        'hostel_id',
        'menu_document_upload',
    ];

    /**
     * Relation: A Mess belongs to a Hotel
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id', 'id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }



    
}
