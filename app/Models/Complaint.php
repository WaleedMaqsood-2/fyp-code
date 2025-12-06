<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'track_id',
        'user_id',
        'subject',
        'description',
        'location',
        'incident_type',
        'incident_datetime',
        'severity',
        'note', 
        'audio_file',
        'transcription',
        'status',
        'is_visible_to_user',
        'assigned_to',
    ];

    // ✅ Add this relationship for transcriptions
    protected $casts = [
        'has_transcription' => 'boolean',
        'is_visible_to_user' => 'boolean'
    ];

    
 // New relationship for transcription
    public function transcription()
    {
        return $this->hasOne(Transcription::class);
    }

    public function transcriptions()
    {
        return $this->hasMany(Transcription::class);
    }

public function latestTranscription()
{
    return $this->hasOne(Transcription::class, 'complaint_id')->latestOfMany();
}

public function verifiedTranscription()
{
    return $this->hasOneThrough(
        TranscriptionVerification::class,
        Transcription::class,
        'complaint_id',
        'media_id',
        'id',
        'id'
    )->where('approved', 1);
}

    // ✅ Add this relationship for transcription verifications
    public function transcriptionVerifications()
    {
        return $this->hasManyThrough(
            TranscriptionVerification::class,
            Transcription::class,
            'complaint_id', // Foreign key on transcriptions table
            'media_id', // Foreign key on transcription_verifications table
            'id', // Local key on complaints table
            'id' // Local key on transcriptions table
        );
    }

    // ✅ Also add these relationships if not present
    public function summaries()
    {
        return $this->hasMany(PendingSummaries::class, 'complaint_id');
    }

    public function reportExports()
    {
        return $this->hasMany(ReportExport::class, 'review_id', 'id');
    }

    // Existing relationships...
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'complaint_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function latestStatus()
    {
        return $this->hasOne(ComplaintStatusLog::class, 'complaint_id')->latestOfMany();
    }
  
    public function PendingSummary()
    {
        return $this->hasOne(PendingSummaries::class, 'complaint_id');
    }

    public function firReport()
    {
        return $this->belongsTo(Complaint::class, 'fir_id', 'id');
    }

    public function latestForensicReview()
    {
        return $this->hasOne(ForensicReview::class, 'fir_id')->latestOfMany();
    }

     // Scope for voice complaints
    public function scopeVoiceComplaints($query)
    {
        return $query->where('complaint_type', 'voice');
    }

    public function scopeWithTranscription($query)
    {
        return $query->where('has_transcription', true);
    }
}