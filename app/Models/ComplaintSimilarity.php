<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintSimilarity extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'has_similar',
        'similarity_score',
        'matched_text',
        'similar_complaint_id',
        'checked_at'
    ];

    // Relationship
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
