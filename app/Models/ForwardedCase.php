<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForwardedCase extends Model
{
    protected $table = 'forensic_reviews'; // ✅ Laravel ko force kar rahe ho correct table use karne ke liye

    protected $fillable = [
        'id',
        'ai_type',
        'feedback_text',
        'rating',
        'created_at',
        'updated_at',
        'user_id',
    ];

    public function users()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
