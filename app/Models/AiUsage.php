<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsage extends Model
{
    protected $table = 'ai_activities';
protected $fillable=[
    'action_type',
    'input_file',
    'output_data',
    'user_id',
    'status',
    'processed_at',
];
    /**
     * Get the count of AI usage records in the ai_usage table.
     * @return int
     */
    public static function countAiUsage()
    {
        return self::where('status', 'success')->count();
    }
}
