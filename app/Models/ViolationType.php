<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationType extends Model
{
    protected $fillable = ['name', 'description'];

    public function disciplinaryViolations()
    {
        return $this->hasMany(DisciplinaryViolation::class);
    }


    
}
