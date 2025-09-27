<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\RecentActivities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        // Sare media files database se lao
      
        $media = \App\Models\Media::paginate(5);
foreach ($media as $file) {
    $absolutePath = Storage::disk('public')->path($file->file_path);

    $file->size = file_exists($absolutePath) 
        ? filesize($absolutePath) 
        : 0;
}


        return view('admin.manage-media   ', compact('media'));
    }


// 🔎 Search API
public function search(Request $request)
{
    $query = $request->q;

    $media = Media::with('complaint')
        ->where('file_path', 'LIKE', "%$query%")
        ->orWhere('description','LIKE',"%$query%")
        ->orWhereHas('complaint', function ($q) use ($query) {
            $q->where('track_id', 'LIKE', "%$query%");
        })
        ->get();

    $html = view('admin.partials.media-search', compact('media'))->render();

    return response()->json(['html' => $html]);
}


// 🔄 Update Status
public function updateStatus(Request $request, $id)
{
    $request->validate(['status' => 'required|in:pending,approved,rejected']);

    $media = Media::findOrFail($id);
    $media->status = $request->status;
    $media->save();

    return back()->with('success', 'Status updated successfully!');
}

// ❌ Delete File
public function destroy($id)
{
    $media = Media::findOrFail($id);


    // Delete actual file from storage
    if (Storage::exists($media->file_path)) {
        Storage::delete($media->file_path);
    }
  RecentActivities::create([
        'user_id' => Auth::id(),
        'action'  => 'Media for ' . optional($media->complaint)->track_id . ' has been deleted.',
    ]);
    $media->delete();

    return back()->with('success', 'Media deleted successfully!');
}

}
