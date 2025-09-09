<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
	// Specify the correct table name
	protected $table = 'media_uploads';

	/**
	 * Get the count of media files in the media_uploads table.
	 * @return int
	 */
	public static function countMediaFiles()
	{
		return self::count();
	}
}
