<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryVerification extends Model
{
    use HasFactory;

    protected $table = 'summaries_verification';
    protected $fillable = [
        'complaint_id',
        'summary_text',
        'user_id',
        'approved_by',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the complaint that owns the verification.
     */
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    /**
     * Get the user who created the verification.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the approver user.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the original summary.
     */
    public function summary()
    {
        return $this->belongsTo(PendingSummaries::class, 'complaint_id', 'complaint_id');
    }

    /**
     * Check if verification is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if verification is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if verification is rejected.
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
}