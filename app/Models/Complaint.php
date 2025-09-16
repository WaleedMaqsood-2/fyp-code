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
        'track_id',
        'assigned_to',
        'note'  // New field for admin notes
        
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
  
//     protected static function booted()
// {
//     static::creating(function ($complaint) {
//         // Latest track_id fetch karo
//         $lastComplaint = Complaint::orderByDesc('id')->first();

//         // Last number extract karo (agar koi complaint pehle se hai)
//         if ($lastComplaint && preg_match('/(\d{6})$/', $lastComplaint->track_id, $matches)) {
//             $lastNumber = (int)$matches[1];
//         } else {
//             $lastNumber = 0;
//         }

//         // Next number banao (001234 → 001235 …)
//         $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

//         // Final Track ID (Prefix + Year + Number)
//         $complaint->track_id = 'CT-' . date('Y') . '-' . $nextNumber;
//     });
// }

}
