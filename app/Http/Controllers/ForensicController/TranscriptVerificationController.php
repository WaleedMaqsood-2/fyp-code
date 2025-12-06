<?php

namespace App\Http\Controllers\ForensicController;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Media;
use App\Models\Transcription;
use App\Models\TranscriptionVerification;
use App\Models\RecentActivities; // Add this
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranscriptVerificationController extends Controller
{
    /**
     * Show transcript verification page
     */
    public function index(Request $request)
    {
        // Get current logged in analyst
        $analystId = Auth::id();
        
        // Query builder for media with transcripts
        $query = Media::whereIn('file_type', ['audio', 'video'])
            ->whereHas('transcription') // Only media with AI transcripts
            ->with([
                'transcription',
                'complaint',
                'transcriptionVerifications' => function($q) use ($analystId) {
                    $q->where('analyst_id', $analystId);
                }
            ]);

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('complaint', function($cq) use ($search) {
                    $cq->where('subject', 'like', "%{$search}%");
                })
                ->orWhereHas('transcription', function($tq) use ($search) {
                    $tq->where('transcript', 'like', "%{$search}%");
                })
                ->orWhereHas('transcriptionVerifications', function($vq) use ($search) {
                    $vq->where('corrected_text', 'like', "%{$search}%");
                })
                ->orWhere('file_path', 'like', "%{$search}%")
                ->orWhere('track_id', 'like', "%{$search}%");
            });
            
            // ✅ **NEW: Transcript search notification**
            NotificationHelper::createForUser(
                $analystId,
                "Transcript Search",
                "You searched for transcripts with keyword: '{$search}'",
                'info',
                route('forensic.transcript')
            );
        }

        // Type filter
        if ($request->has('type') && !empty($request->type)) {
            $query->where('file_type', $request->type);
        }

        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status == 'verified') {
                $query->whereHas('transcriptionVerifications', function($q) use ($analystId) {
                    $q->where('analyst_id', $analystId)
                      ->where('approved', 1);
                });
            } elseif ($request->status == 'pending') {
                $query->where(function($q) use ($analystId) {
                    $q->whereDoesntHave('transcriptionVerifications', function($vq) use ($analystId) {
                        $vq->where('analyst_id', $analystId);
                    })->orWhereHas('transcriptionVerifications', function($vq) use ($analystId) {
                        $vq->where('analyst_id', $analystId)
                           ->where('approved', 0);
                    });
                });
            }
        }

        // Pagination
        $evidences = $query->paginate(15)->withQueryString();

        // Calculate statistics for current analyst
        $videoCount = Media::where('file_type', 'video')
            ->whereHas('transcription')
            ->count();
            
        $audioCount = Media::where('file_type', 'audio')
            ->whereHas('transcription')
            ->count();
            
        $verifiedCount = TranscriptionVerification::where('analyst_id', $analystId)
            ->where('approved', 1)
            ->count();
            
        $pendingCount = Media::whereIn('file_type', ['audio', 'video'])
            ->whereHas('transcription')
            ->where(function($q) use ($analystId) {
                $q->whereDoesntHave('transcriptionVerifications', function($vq) use ($analystId) {
                    $vq->where('analyst_id', $analystId);
                })->orWhereHas('transcriptionVerifications', function($vq) use ($analystId) {
                    $vq->where('analyst_id', $analystId)
                       ->where('approved', 0);
                });
            })->count();

        // ✅ **NEW: Transcript verification dashboard access notification**
        if (!session()->has('transcript_verification_accessed')) {
            $message = "You have {$pendingCount} pending transcript verifications and {$verifiedCount} verified.";
            NotificationHelper::createForUser(
                $analystId,
                "Transcript Verification Dashboard",
                $message,
                'info',
                route('forensic.transcript')
            );
            session(['transcript_verification_accessed' => true]);
        }

        return view('forensic_analyst.transcript-verification', compact(
            'evidences',
            'videoCount',
            'audioCount',
            'verifiedCount',
            'pendingCount'
        ));
    }

    /**
     * Save/Update Verified Transcript
     */
    public function update(Request $request)
    {
        $request->validate([
            'media_id' => 'required|integer|exists:media_uploads,id',
            'corrected_text' => 'required|string|max:10000',
            'approved' => 'nullable|boolean',
        ]);

        // Get current analyst
        $analystId = Auth::id();
        
        // Get media to get complaint_id
        $media = Media::findOrFail($request->media_id);
        $case = Complaint::find($media->complaint_id);
        $isApproved = $request->has('approved') ? $request->approved : 0;

        // Create or update verification record
        $verification = TranscriptionVerification::updateOrCreate(
            [
                'media_id' => $request->media_id,
                'analyst_id' => $analystId,
            ],
            [
                'complaint_id' => $media->complaint_id,
                'corrected_text' => $request->corrected_text,
                'approved' => $isApproved,
            ]
        );

        // ✅ **NEW: Transcript verification notification**
        $actionText = $isApproved ? 'verified and approved' : 'saved';
        NotificationHelper::createForUser(
            $analystId,
            "Transcript {$actionText}",
            "You {$actionText} transcript for media #{$media->id}",
            $isApproved ? 'success' : 'info',
            route('forensic.transcript')
        );

        // ✅ **NEW: Notify admin about transcript verification
        if ($isApproved && $case) {
            $admins = \App\Models\User::where('role_id', 1)->active()->get();
            foreach ($admins as $admin) {
                NotificationHelper::createForUser(
                    $admin->id,
                    "Transcript Verified",
                    "Forensic analyst verified transcript for case #{$case->track_id}",
                    'info',
                    route('admin.complaints.show', $case->id)
                );
            }
        }

        // Log activity
        RecentActivities::create([
            'user_id' => $analystId,
            'action'  => "Transcript {$actionText} for media #{$media->id}",
        ]);

        return back()->with('success', 'Transcript verification saved successfully.');
    }

    /**
     * Get verification status for a media
     */
    public function getVerificationStatus($mediaId)
    {
        $analystId = Auth::id();
        
        $verification = TranscriptionVerification::where('media_id', $mediaId)
            ->where('analyst_id', $analystId)
            ->first();
            
        return response()->json([
            'exists' => $verification ? true : false,
            'corrected_text' => $verification ? $verification->corrected_text : '',
            'approved' => $verification ? $verification->approved : 0,
            'created_at' => $verification ? $verification->created_at : null,
        ]);
    }
}