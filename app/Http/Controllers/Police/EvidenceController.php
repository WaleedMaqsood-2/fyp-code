<?php

namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\RecentActivities;
use App\Models\Complaint; // Add this
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    public function index()
    {
        // Sare media files database se lao
        $media = \App\Models\Media::where('user_id', Auth::id())->paginate(5);
        
        foreach ($media as $file) {
            $absolutePath = Storage::disk('public')->path($file->file_path);
            $file->size = file_exists($absolutePath) 
                ? filesize($absolutePath) 
                : 0;
        }

        // ✅ **NEW: Evidence dashboard access notification**
        if (!session()->has('evidence_dashboard_accessed')) {
            $totalEvidence = Media::where('user_id', Auth::id())->count();
            NotificationHelper::createForUser(
                Auth::id(),
                "Evidence Management",
                "You accessed evidence management dashboard. Total: {$totalEvidence} files",
                'info',
                route('police.evidence.store')
            );
            session(['evidence_dashboard_accessed' => true]);
        }

        return view('police.upload-evidence', compact('media'));
    }

    // Store uploaded files
    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // max 10MB
            'complaint_id' => 'required|numeric',
            'file_type' => 'required|string',
        ]);

        $officerId = Auth::check() ? Auth::id() : null;
        $fileCount = 0;
        $case = Complaint::find($request->complaint_id);
        $trackId = $case ? $case->track_id : 'Unknown';

        foreach ($request->file('files') as $file) {
            if ($file->isValid()) {
                $fileCount++;
                $path = $file->store('media_uploads', 'public');

                // Detect file type from extension
                $extension = strtolower($file->getClientOriginalExtension());

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $fileType = 'image';
                } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'mkv'])) {
                    $fileType = 'video';
                } elseif (in_array($extension, ['mp3', 'wav', 'aac'])) {
                    $fileType = 'audio';
                } elseif (in_array($extension, ['zip', 'rar'])) {
                    $fileType = 'archive';
                } else {
                    $fileType = 'document';
                }

                Media::create([
                    'user_id' => $officerId,
                    'file_type' => $request->file_type,
                    'file_path' => $path,
                    'complaint_id' => $request->complaint_id,
                    'status' => 'pending',
                ]);

                // Log upload in activity
                RecentActivities::create([
                    'user_id' => $officerId,
                    'action'  => 'Uploaded ' . $fileType . ' evidence for FIR.',
                ]);
            }
        }

        // ✅ **NEW: Evidence upload notification**
        if ($fileCount > 0) {
            NotificationHelper::createForUser(
                $officerId,
                "Evidence Uploaded",
                "You uploaded {$fileCount} evidence files for case #{$trackId}",
                'success',
                route('police.cases')
            );

            // ✅ **NEW: Notify admin about evidence upload
            $admins = \App\Models\User::where('role_id', 1)->active()->get();
            foreach ($admins as $admin) {
                NotificationHelper::createForUser(
                    $admin->id,
                    "New Evidence Uploaded",
                    "Police officer uploaded {$fileCount} evidence files for case #{$trackId}",
                    'info',
                    route('admin.complaints.show', $request->complaint_id)
                );
            }

            // ✅ **NEW: Notify forensic analyst if case is assigned
            if ($case && $case->assigned_to) {
                NotificationHelper::createForUser(
                    $case->assigned_to,
                    "New Evidence Available",
                    "Police officer uploaded {$fileCount} new evidence files for case #{$trackId}",
                    'info',
                    route('forensic.case.details', $case->id)
                );
            }
        }

        return redirect()->back()->with('success', 'Evidence uploaded successfully!');
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

        // ✅ **NEW: Evidence search notification**
        if ($query) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Evidence Search",
                "You searched for evidence with keyword: '{$query}'",
                'info',
                route('police.evidence.store')
            );
        }

        $html = view('admin.partials.media-search', compact('media'))->render();

        return response()->json(['html' => $html]);
    }

    // 🔄 Update Status
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);

        $media = Media::findOrFail($id);
        $oldStatus = $media->status;
        $media->status = $request->status;
        $media->save();

        // ✅ **NEW: Evidence status update notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Evidence Status Updated",
            "You updated evidence file status from '{$oldStatus}' to '{$request->status}'",
            'info',
            route('police.evidence.store')
        );

        return back()->with('success', 'Status updated successfully!');
    }

    // ❌ Delete File
    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        $filePath = $media->file_path;
        $trackId = optional($media->complaint)->track_id;

        // ✅ **NEW: Evidence deletion notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Evidence Deleted",
            "You deleted evidence file for case #{$trackId}",
            'warning',
            route('police.evidence.store')
        );

        // ✅ **NEW: Notify admin about evidence deletion
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Evidence File Deleted",
                "Police officer deleted evidence file for case #{$trackId}",
                'warning',
                route('admin.complaints.show', $media->complaint_id)
            );
        }

        // Delete actual file from storage
        if (Storage::exists($media->file_path)) {
            Storage::delete($media->file_path);
        }
        
        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'Media for ' . $trackId . ' has been deleted.',
        ]);

        $media->delete();

        return back()->with('success', 'Media deleted successfully!');
    }
}