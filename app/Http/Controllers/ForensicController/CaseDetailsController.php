<?php

namespace App\Http\Controllers\ForensicController;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ForensicReview;
use App\Models\Transcription;
use App\Models\PendingSummaries;
use App\Models\TranscriptionVerification;
use App\Models\SummaryVerification;
use App\Models\RecentActivities; // Add this if not exists
use App\Helpers\NotificationHelper; // Add this

class CaseDetailsController extends Controller
{
    public function CaseDetails($id)
    {
        $case = Complaint::with([
            'user',
            'media',
            'summaries' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'transcriptions' => function($query) {
                $query->with('verifications')
                      ->orderBy('created_at', 'desc');
            },
            'latestForensicReview'
        ])->findOrFail($id);

        $evidences = Media::where('complaint_id', $id)->get();

        // Get latest verified transcription if exists
        $verifiedTranscription = null;
        foreach ($case->transcriptions as $transcription) {
            if ($transcription->verifications && $transcription->verifications->isNotEmpty()) {
                $verification = $transcription->verifications->first();
                if ($verification->approved) {
                    $verifiedTranscription = $verification->corrected_text;
                    break;
                }
            }
        }

        // Get latest approved summary
        $approvedSummary = $case->summaries
            ->where('status', 'approved')
            ->first();

        // ✅ **NEW: Case details view notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Case Details Viewed",
            "You viewed details for case #{$case->track_id}",
            'info',
            route('forensic.case.details', $id)
        );

        return view('forensic_analyst.case-details', compact(
            'case', 
            'evidences',
            'verifiedTranscription',
            'approvedSummary'
        ));
    }

    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string',
            'status' => 'required|in:pending,approved,rejected,analyzing,completed'
        ]);

        $case = Complaint::findOrFail($id);
        $oldStatus = $case->status;

        // Save review in forensic_reviews table
        $review = ForensicReview::create([
            'analyst_id' => Auth::id(),
            'fir_id'     => $case->id,
            'findings'   => $request->notes,
            'status'     => $request->status,
        ]);

        // Update case status
        $case->status = $request->status;
        $case->save();

        // Update media status
        $mediaItems = Media::where('complaint_id', $id)->get();
        foreach ($mediaItems as $media) {
            $media->status = $request->status;
            $media->save();
        }

        // ✅ **NEW: Review submission notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Forensic Review Submitted",
            "You submitted review for case #{$case->track_id}. Status: {$request->status}",
            'success',
            route('forensic.case.details', $id)
        );

        // ✅ **NEW: Notify complaint owner about status change
        if ($case->user_id) {
            NotificationHelper::publicCaseStatusUpdate(
                $case->user_id,
                $case->id,
                $request->status
            );
        }

        // ✅ **NEW: Notify police officer about forensic review
        if ($case->assigned_to) {
            NotificationHelper::createForUser(
                $case->assigned_to,
                "Forensic Review Complete",
                "Forensic review completed for case #{$case->track_id}. Status: {$request->status}",
                'info',
                route('police.cases.show', $id)
            );
        }

        // ✅ **NEW: Notify admins about forensic review
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Forensic Review Submitted",
                "Forensic analyst {$review->analyst->name} submitted review for case #{$case->track_id}",
                'info',
                route('admin.complaints.show', $id)
            );
        }

        // Log activity
        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'Forensic review submitted for case #' . $case->track_id . '. Status: ' . $request->status,
        ]);

        return back()->with('success', 'Review submitted successfully.');
    }
}