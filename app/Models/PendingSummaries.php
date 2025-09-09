<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingSummaries extends Model
{
    protected $table = 'summaries';
protected $fillable=[
    'user_id',
    'summary_text',
    'status',
    'approved_by',
];
    /**
     * Get the count of pending summaries in the pending_summaries table.
     * @return int
     */
    public static function countPendingSummaries()
    {
        return self::where('status', 'pending')->count();
    }
}
