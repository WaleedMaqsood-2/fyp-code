<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;

class MediaController extends Controller
{
    public function index()
    {
        // Sare media files database se lao
        $media = Media::latest()->paginate(10); // paginate 10 items
        return view('admin.manage-media   ', compact('media'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $media = Media::where('description', 'like', "%$query%")
            ->orWhere('file_type', 'like', "%$query%")
            ->orWhere('complaint_id', 'like', "%$query%")
            ->latest()
            ->get();

        return response()->json([
            'media' => $media
        ]);
    }
}
