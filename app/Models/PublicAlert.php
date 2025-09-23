<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicAlert extends Model
{
    use HasFactory;
    protected $table = 'public_alerts';
    protected $fillable = [
        'title',
        'message',
        'type',
        'visible_until',
        'media',
        'user_id', // jo alert create karega uska user ID
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}
 protected $casts = [
        'media' => 'array', // Laravel automatically JSON encode/decode karega
    ];
 
}
