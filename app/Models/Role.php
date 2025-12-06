<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
        'description'
    ];

    /**
     * Relationship with users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Get active users count
     */
    public function activeUsersCount()
    {
        return $this->users()->where('status', 'active')->count();
    }
    
    /**
     * Scope for specific role
     */
    public function scopeByName($query, $roleName)
    {
        return $query->where('role_name', $roleName);
    }
}