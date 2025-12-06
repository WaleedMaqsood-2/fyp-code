<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Complaint;
use App\Models\Media;
use App\Models\User;
use App\Models\TranscriptionVerification;


class Transcription extends Model
{
  
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'media_id',
        'transcript',
        'original_text',
        'roman_text',
        'audio_path',
        'language',
        'status',
        'confidence_score',
        'user_id'
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    
    // Relationships
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



   public function verifications()
{
    return $this->hasMany(TranscriptionVerification::class, 'transcription_id', 'id');
}


    /**
     * Get the latest verification.
     */
 public function latestVerification()
{
    return $this->hasOne(TranscriptionVerification::class, 'transcription_id', 'id')
                ->latest();
}


    /**
     * Get the verification by specific analyst.
     */
 public function verificationByAnalyst($analystId)
{
    return $this->hasOne(TranscriptionVerification::class, 'transcription_id', 'id')
                ->where('analyst_id', $analystId);
}


 
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get all verifications for the transcription's media.
     */

    /**
     * Check if transcript has any approved verification.
     */
    public function hasApprovedVerification()
    {
        return $this->verifications()->where('approved', true)->exists();
    }

    /**
     * Get the approved verification.
     */
    public function approvedVerification()
{
    return $this->hasOne(TranscriptionVerification::class, 'transcription_id', 'id')
                ->where('approved', true);
}

}
