<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Define the relationship
// App\Models\User.php
public function role()
{
    return $this->belongsTo(Role::class, 'role_id');
}

public function media()
{
    return $this->hasMany(\App\Models\Media::class, 'user_id');
}
public function aiFeedbacks()
{
    return $this->hasMany(\App\Models\AIFeedback::class, 'user_id');
}
protected $fillable = [
    'name',
    'email',
    'role_id',
    'password',
    'cnic',
    'contact_number',
    'profile_image',
    'otp',
    'otp_expires_at',
    'is_verified',
    'reg_status',
    'status',
    'email_verified_at',
];

protected $table = 'users';

/**
 * Get the count of users in the users table.
 * @return int
 */
public static function countUsers()
{
    return self::count();
}
}