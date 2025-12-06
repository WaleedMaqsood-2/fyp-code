<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenceSegment extends Model
{
    use HasFactory;
    protected $table = 'evidence_segments';
    protected $fillable = [
        'media_id',
        'complaint_id',
        'segment_file',
        'start_time',
        'end_time',
        'file_extension',
    ];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
