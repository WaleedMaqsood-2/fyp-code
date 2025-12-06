<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;



  class PendingSummaries extends Model
{
      use HasFactory;
    protected $table = 'summaries';
protected $fillable=[
    'user_id',
    'summary_text',
    'status',
    'approved_by',
    'complaint_id',

];
      public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

      public function generatedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public static function countPendingSummaries()
    {
        return self::where('status', 'pending')->count();
    }

    


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

 
    

    /**
     * Get the user who created the summary.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the verification record for this summary.
     */
    public function verification()
    {
        return $this->hasOne(SummaryVerification::class, 'complaint_id', 'complaint_id');
    }

    /**
     * Check if summary is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if summary is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if summary is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Get status badge.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-warning">Pending</span>'
        };
    }

    /**
     * Scope for pending summaries.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved summaries.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected summaries.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}


