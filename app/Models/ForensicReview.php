<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForensicReview extends Model
{
    protected $table = 'forensic_reviews';

    protected $fillable = [
        'analyst_id',
        'fir_id',
        'findings',
        'status',
    ];
}
