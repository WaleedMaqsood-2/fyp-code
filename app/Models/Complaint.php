<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    // ✅ Add these fields to allow mass assignment
    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'location',
        'incident_type',
        'severity',
        'status',
        
    ];

    // Optional: relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'complaint_id');
    }
  
}
