<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
	// Specify the correct table name
	protected $table = 'media_uploads';
 public function users()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	public function complaint()
{
    return $this->belongsTo(Complaint::class);
}
protected $fillable = [
		'user_id',
		'file_type',
		'file_path',
		'description',
		'status',
		'uploaded_at',
		'complaint_id'
	];
	/**
	 * Get the count of media files in the media_uploads table.
	 * @return int
	 */
	public static function countMediaFiles()
	{
		return self::count();
	}
}
