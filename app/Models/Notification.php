<?php

namespace App\Models;

use App\Helpers\NotificationHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Notification extends Model
{
    use HasFactory;
protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'module',
        'link',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
    
    /**
     * Get user's role
     */
    public function getUserRoleAttribute()
    {
        return $this->user ? \App\Helpers\NotificationHelper::getReadableRoleName($this->user->role_id) : 'N/A';
    }
    
    /**
     * Check if notification is unread
     */
    public function getIsUnreadAttribute()
    {
        return is_null($this->read_at);
    }
    
    /**
     * Get time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
    
    /**
     * Get icon
     */
    public function getIconAttribute()
    {
        return \App\Helpers\NotificationHelper::getIcon($this->type);
    }
    
    /**
     * Get CSS class
     */
    public function getCssClassAttribute()
    {
        return \App\Helpers\NotificationHelper::getCssClass($this->type);
    }
    
    /**
     * Mark as read
     */
    public function markAsRead()
    {
        if ($this->isUnread) {
            $this->read_at = now();
            $this->save();
        }
        return $this;
    }
    
    
    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
    
    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }
    
    /**
     * Scope by user role
     */
    public function scopeByUserRole($query, $roleId)
    {
        return $query->whereHas('user', function($q) use ($roleId) {
            $q->where('role_id', $roleId);
        });
    }
    
    /**
     * Scope by module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }
    
    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
 

public static function markAllAsRead($userId)
{
    return self::where('user_id', $userId)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);
}

}