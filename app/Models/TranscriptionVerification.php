<?php

namespace App\Models;

use App\Models\Complaint;
use App\Models\Media;
use App\Models\Transcription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class TranscriptionVerification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transcription_verifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'transcription_id',
        'complaint_id',
        'media_id',
        'analyst_id',
        'corrected_text',
        'corrected_roman',
        'approved',
        'notes',
        'verified_at'
    ];

    protected $casts = [
        'approved' => 'boolean',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
  

    /**
     * Get the complaint that owns the verification.
     */
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    /**
     * Get the media that owns the verification.
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }


    /**
     * Get the analyst who performed the verification.
     */
    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    
    /**
     * Get the original AI transcription.
     */
    public function transcription()
    {
        return $this->belongsTo(Transcription::class, 'media_id', 'media_id');
    }


    
 

    /**
     * Scope a query to only include approved verifications.
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Scope a query to only include pending verifications.
     */
    public function scopePending($query)
    {
        return $query->where('approved', false);
    }

    /**
     * Scope a query to only include verifications by a specific analyst.
     */
    public function scopeByAnalyst($query, $analystId)
    {
        return $query->where('analyst_id', $analystId);
    }

    /**
     * Check if verification is approved.
     */
    public function isApproved()
    {
        return $this->approved === true;
    }

    /**
     * Check if verification is pending.
     */
    public function isPending()
    {
        return $this->approved === false;
    }

    /**
     * Get the word count of corrected text.
     */
    public function getWordCountAttribute()
    {
        return str_word_count($this->corrected_text);
    }

    /**
     * Get the character count of corrected text.
     */
    public function getCharacterCountAttribute()
    {
        return strlen($this->corrected_text);
    }

    /**
     * Get the verification status badge.
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->approved) {
            return '<span class="badge bg-success"><i class="material-icons align-middle" style="font-size:14px;">verified</i> Approved</span>';
        } else {
            return '<span class="badge bg-warning"><i class="material-icons align-middle" style="font-size:14px;">edit</i> Draft</span>';
        }
    }

    /**
     * Get the verification status text.
     */
    public function getStatusTextAttribute()
    {
        return $this->approved ? 'Approved' : 'Draft';
    }

    /**
     * Get the verification status color.
     */
    public function getStatusColorAttribute()
    {
        return $this->approved ? 'success' : 'warning';
    }
}


