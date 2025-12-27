<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;


// use Illuminate\Database\Eloquent\Casts\Attribute;
// use Illuminate\Support\Facades\Crypt;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    // ... existing properties and methods ...
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // protected function email(): Attribute
    // {
    //     return Attribute::make(
    //         get: function ($value) {
    //             try {
    //                 return Crypt::decryptString($value);
    //             } catch (\Exception $e) {
    //                 // If decrypt fails, return as-is (plaintext)
    //                 return $value;
    //             }
    //         },
    //         set: function ($value) {
    //             try {
    //                 // Prevent double encryption (if already encrypted)
    //                 Crypt::decryptString($value);
    //                 return $value;
    //             } catch (\Exception $e) {
    //                 return Crypt::encryptString($value);
    //             }
    //         },
    //     );
    // }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function managedUsers()
    {
        return $this->hasMany(User::class, 'manager_id');
    }


}
