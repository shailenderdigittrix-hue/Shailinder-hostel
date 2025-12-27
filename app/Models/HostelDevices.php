<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelDevices extends Model
{
    protected $table = 'hostel_devices';
    protected $fillable = [
        'hostel_id',
        'device_serial_no',
        'device_name',
      ];
}
