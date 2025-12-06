<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\RecentActivities;
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $media = \App\Models\Media::paginate(5);
        foreach ($media as $file) {
            $absolutePath = Storage::disk('public')->path($file->file_path);
            $file->size = file_exists($absolutePath) 
                ? filesize($absolutePath) 
                : 0;
        }
        
        // ✅ **NEW: Media dashboard access notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Media Management",
            "You accessed the media management dashboard",
            'info',
            route('manage.media')
        );

        return view('admin.manage-media', compact('media'));
    }

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

        // ✅ **NEW: Media search notification**
        if ($query) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Media Search",
                "You searched for media files with keyword: '{$query}'",
                'info',
                route('manage.media')
            );
        }

        $html = view('admin.partials.media-search', compact('media'))->render();

        return response()->json(['html' => $html]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);

        $media = Media::findOrFail($id);
        $oldStatus = $media->status;
        $media->status = $request->status;
        $media->save();

        // ✅ **NEW: Media status update notification to admin**
        NotificationHelper::createForUser(
            Auth::id(),
            "Media Status Updated",
            "Media file status changed from '{$oldStatus}' to '{$request->status}'",
            'info',
            route('manage.media')
        );

        // ✅ **NEW: Media status update notification to uploader**
        if ($media->user_id) {
            $statusText = $request->status == 'approved' ? 'approved ✅' : 
                         ($request->status == 'rejected' ? 'rejected ❌' : $request->status);
            
            NotificationHelper::createForUser(
                $media->user_id,
                "Media File Status Updated",
                "Your uploaded file has been {$statusText}",
                $request->status == 'approved' ? 'success' : 
                ($request->status == 'rejected' ? 'danger' : 'info'),
                $media->complaint_id ? route('public.complaints.track', ['track_id' => $media->complaint->track_id]) : '#'
            );
        }

        return back()->with('success', 'Status updated successfully!');
    }

    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        $filePath = $media->file_path;
        $complaintTrackId = optional($media->complaint)->track_id;

        // ✅ **NEW: Pre-deletion notification to uploader**
        if ($media->user_id) {
            NotificationHelper::createForUser(
                $media->user_id,
                "Media File Deleted",
                "Your uploaded file has been deleted by administrator",
                'warning',
                $media->complaint_id ? route('public.complaints.track', ['track_id' => $complaintTrackId]) : '#'
            );
        }

        // Delete actual file from storage
        if (Storage::exists($media->file_path)) {
            Storage::delete($media->file_path);
        }
        
        // ✅ **NEW: Deletion notification to admin**
        NotificationHelper::createForUser(
            Auth::id(),
            "Media File Deleted",
            "You deleted media file: " . basename($filePath),
            'warning',
            route('manage.media')
        );

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'Media for ' . $complaintTrackId . ' has been deleted.',
        ]);

        $media->delete();

        return back()->with('success', 'Media deleted successfully!');
    }
}