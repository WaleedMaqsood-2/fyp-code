<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceMatch extends Model
{
    use HasFactory;

    protected $table = 'face_matches';
    
    protected $fillable = [
        'complaint_id',
        'media_id',
        'reference_image_path',
        'matched_image_path',
        'confidence',
        'match_details',
        'analyst_id',
        'status',
        'notes'
    ];

    protected $casts = [
        'confidence' => 'float',
        'match_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the complaint that owns the face match.
     */
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    /**
     * Get the media that owns the face match.
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get the analyst who performed the match.
     */
    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    /**
     * Get confidence badge.
     */
    public function getConfidenceBadgeAttribute()
    {
        if ($this->confidence >= 80) {
            return '<span class="badge bg-success">' . $this->confidence . '%</span>';
        } elseif ($this->confidence >= 60) {
            return '<span class="badge bg-warning">' . $this->confidence . '%</span>';
        } else {
            return '<span class="badge bg-danger">' . $this->confidence . '%</span>';
        }
    }

    /**
     * Get status badge.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'verified' => '<span class="badge bg-success">Verified</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-warning">Pending</span>'
        };
    }

    /**
     * Scope for verified matches.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope for pending matches.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for high confidence matches.
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence', '>=', 80);
    }
}