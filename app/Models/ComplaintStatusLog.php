<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'police_officer',
        'forwarded_to',
        'status',
        'note',
        'changed_at',
    ];

    // Optional relationships
   


public function complaint()
{
    return $this->belongsTo(Complaint::class, 'complaint_id');
}

public function officer()
{
    return $this->belongsTo(User::class, 'police_officer');
}


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forwardedUser()
    {
        return $this->belongsTo(User::class, 'forwarded_to');
    }
}
