<?php

namespace App\Models;

use App\Models\Complaint;
use App\Models\EvidenceSegment;
use App\Models\Transcription;
use App\Models\TranscriptionVerification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class Media extends Model
{
	// Specify the correct table name
	protected $table = 'media_uploads';
 public function users()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	public function complaint()
{
    return $this->belongsTo(Complaint::class, 'complaint_id');
}
public function transcription()
{
    return $this->hasOne(Transcription::class,'media_id');
}

public function segments()
{
    return $this->hasMany(EvidenceSegment::class);
}

  /**
     * Get the transcription verification for the media by current analyst.
     */
    public function transcriptionVerifications()
    {
        return $this->hasMany(TranscriptionVerification::class, 'media_id');
    }

    /**
     * Get the transcription verification for the media by specific analyst.
     */
    public function transcriptionVerificationByAnalyst($analystId = null)
    {
        if (!$analystId) {
            $analystId = Auth::id();
        }
        
        return $this->hasOne(TranscriptionVerification::class, 'media_id')
            ->where('analyst_id', $analystId);
    }

    /**
     * Get the latest transcription verification.
     */
    public function latestTranscriptionVerification()
    {
        return $this->hasOne(TranscriptionVerification::class, 'media_id')
            ->latest();
    }

protected $fillable = [
		'user_id',
		'file_type',
		'file_path',
		'description',
		'status',
		'uploaded_at',
		'complaint_id'
	];
	/**
	 * Get the count of media files in the media_uploads table.
	 * @return int
	 */
	public static function countMediaFiles()
	{
		return self::count();
	}
	
}