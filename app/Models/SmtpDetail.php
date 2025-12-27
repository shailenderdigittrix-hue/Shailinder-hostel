<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtpDetail extends Model
{
    use HasFactory;

    protected $table = 'smtp_settings';

    protected $primaryKey = 'id';

    
    protected $fillable = [
        'mailer',
        'scheme',
        'host',
        'port',
        'username',
        'password',
        'from_address',
        'from_name',
        'encryption',
        'status',
    ];

    public $timestamps = true;
}
