<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReportExport extends Model
{
    use HasFactory;

    protected $table = 'report_exports';
    protected $fillable = [
        'review_id',
        'file_path',
        'exported_at',
    ];

    protected $casts = [
        'exported_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the complaint that owns the report export.
     */
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'review_id', 'id');
    }

    /**
     * Get file size in readable format.
     */
 // ReportExport.php model میں
/**
 * Check if file exists.
 */
public function fileExists()
{
    return Storage::disk('public')->exists($this->file_path);
}

/**
 * Get file size in readable format.
 */
public function getFileSizeAttribute()
{
    if (!$this->fileExists()) {
        return 'N/A';
    }

    $bytes = Storage::disk('public')->size($this->file_path);
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}
}