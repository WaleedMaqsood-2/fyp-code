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


    protected $hidden = [
        'password',
        'remember_token',
        'otp'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

     /**
     * Get role name
     */
    public function getRoleNameAttribute()
    {
        return $this->role ? strtolower($this->role->role_name) : 'user';
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole($roleName)
    {
        return $this->role && strtolower($this->role->role_name) === strtolower($roleName);
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Check if user is police
     */
    public function isPolice()
    {
        return $this->hasRole('police');
    }
    
    /**
     * Check if user is forensic analyst
     */
    public function isForensic()
    {
        return $this->hasRole('forensic analyst');
    }
    
    /**
     * Check if user is public user
     */
    public function isPublic()
    {
        return $this->hasRole('public user');
    }
    
    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', 1);
    }
    
    /**
     * Scope by role
     */
    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }
    
    /**
     * Relationship with notifications
     */
   public function notifications()
{
    return $this->hasMany(\App\Models\Notification::class, 'user_id');
}

    /**
     * Unread notifications
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }
    
    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount()
    {
        return $this->unreadNotifications()->count();
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        return $this->unreadNotifications()->update(['read_at' => now()]);
    }
    public function customNotifications()
{
    return $this->hasMany(\App\Models\Notification::class, 'user_id');
}

    /**
     * Get latest notifications
     */
    public function latestNotifications($limit = 10)
    {
        return $this->notifications()->limit($limit)->get();
    }
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