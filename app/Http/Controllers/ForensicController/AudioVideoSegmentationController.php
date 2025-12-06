<?php
namespace App\Http\Controllers\ForensicController;

use App\Helpers\NotificationHelper; // Add this
use App\Http\Controllers\Controller;
use App\Models\EvidenceSegment;
use App\Models\Media;
use App\Models\RecentActivities; // Add this if not exists
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AudioVideoSegmentationController extends Controller
{
    // Show the segmentation page
    public function index(Request $request)
    {
        // Query builder شروع کریں
        $query = Media::whereIn('file_type', ['audio', 'video'])
            ->with('segments');

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('track_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_path', 'like', "%{$search}%");
            });
            
            // ✅ **NEW: Media search notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "Media Segmentation Search",
                "You searched for media with keyword: '{$search}'",
                'info',
                route('forensic.audio-video')
            );
        }

        // Type filter
        if ($request->has('type') && !empty($request->type)) {
            $query->where('file_type', $request->type);
        }

        // Pagination with 10 items per page
        $evidences = $query->paginate(10)->withQueryString();

        // ✅ **NEW: Segmentation dashboard access notification**
        if (!session()->has('segmentation_accessed')) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Media Segmentation Dashboard",
                "You accessed the media segmentation dashboard",
                'info',
                route('forensic.audio-video')
            );
            session(['segmentation_accessed' => true]);
        }

        return view('forensic_analyst.audio-video-segmentation', compact('evidences'));
    }

    // Handle segmentation form submission
    public function segment(Request $request)
    {
        $request->validate([
            'evidence_id' => 'required|integer|exists:media_uploads,id',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
        ]);

        $evidence = Media::findOrFail($request->evidence_id);
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        // Input & Output Paths
        $inputPath = storage_path('app/public/'.$evidence->file_path);
        $outputDir = storage_path('app/public/evidence_segments/');
        if (!file_exists($outputDir)) mkdir($outputDir, 0777, true);

        // Generate unique filename
        $timestamp = time();
        $randomString = bin2hex(random_bytes(4));
        
        if ($evidence->file_type === 'audio') {
            // Convert audio to browser-friendly mp3
            $outputFileName = 'audio_'.$evidence->id.'_'.$timestamp.'_'.$randomString.'.mp3';
            $outputPath = $outputDir.$outputFileName;
            $cmd = "ffmpeg -i \"{$inputPath}\" -ss {$startTime} -to {$endTime} -c:a libmp3lame -b:a 192k -y \"{$outputPath}\" 2>&1";
            $fileExtension = 'mp3';
        } else {
            // Convert video to mp4 (H264 + AAC)
            $outputFileName = 'video_'.$evidence->id.'_'.$timestamp.'_'.$randomString.'.mp4';
            $outputPath = $outputDir.$outputFileName;
            $cmd = "ffmpeg -i \"{$inputPath}\" -ss {$startTime} -to {$endTime} -c:v libx264 -preset fast -c:a aac -b:a 128k -strict experimental -y \"{$outputPath}\" 2>&1";
            $fileExtension = 'mp4';
        }

        // Execute command and check output
        exec($cmd, $output, $returnCode);
        
        if ($returnCode !== 0) {
            // Log error for debugging
            Log::error('FFMPEG Error: ' . implode("\n", $output));
            
            // ✅ **NEW: Segmentation failure notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "Segmentation Failed",
                "Failed to segment media #{$evidence->id}. Error code: {$returnCode}",
                'danger',
                route('forensic.audio-video')
            );
            
            return back()->with('error', "Failed to segment media. Please check the file format and try again.");
        }

        // Check if file was created
        if (!file_exists($outputPath)) {
            // ✅ **NEW: File creation failure notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "Segmentation Failed",
                "Failed to create segment file for media #{$evidence->id}",
                'danger',
                route('forensic.audio-video')
            );
            
            return back()->with('error', "Failed to create segment file.");
        }

        // Save segment in DB
        $segment = EvidenceSegment::create([
            'media_id' => $evidence->id,
            'complaint_id' => $evidence->complaint_id,
            'segment_file' => 'evidence_segments/'.$outputFileName,
            'file_extension' => $fileExtension,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'media_type' => $evidence->file_type,
        ]);

        // ✅ **NEW: Successful segmentation notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Media Segmentation Successful",
            "Segmented evidence #{$evidence->id} successfully from {$startTime} to {$endTime}",
            'success',
            route('forensic.audio-video')
        );

        // ✅ **NEW: Notify admin about segmentation
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Media Segmentation Complete",
                "Forensic analyst segmented media file for case #" . ($evidence->complaint->track_id ?? 'Unknown'),
                'info',
                route('admin.complaints.show', $evidence->complaint_id)
            );
        }

        // Log activity
        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'Media #' . $evidence->id . ' segmented from ' . $startTime . ' to ' . $endTime,
        ]);

        return back()->with('success', "Segmented evidence #{$evidence->id} successfully from {$startTime} to {$endTime}.");
    }
}