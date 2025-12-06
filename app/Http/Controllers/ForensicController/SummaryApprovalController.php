<?php

namespace App\Http\Controllers\ForensicController;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\PendingSummaries as Summary;
use App\Models\SummaryVerification;
use App\Models\RecentActivities; // Add this
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SummaryApprovalController extends Controller
{
    /**
     * Show AI Summary Approval page
     */
    public function index(Request $request)
    {
        // Query for pending summaries that need approval
        $query = Summary::with(['complaint', 'user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('complaint', function($cq) use ($search) {
                    $cq->where('track_id', 'like', "%{$search}%")
                       ->orWhere('subject', 'like', "%{$search}%");
                });
            });
            
            // ✅ **NEW: Summary search notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "Summary Search",
                "You searched for summaries with keyword: '{$search}'",
                'info',
                route('forensic.summary.approved')
            );
        }

        $summaries = $query->paginate(10)->withQueryString();

        // Get statistics
        $stats = [
            'pending' => Summary::where('status', 'pending')->count(),
            'approved' => Summary::where('status', 'approved')->count(),
            'rejected' => Summary::where('status', 'rejected')->count(),
            'total' => Summary::count(),
        ];

        // ✅ **NEW: Summary approval dashboard access notification**
        if (!session()->has('summary_approval_accessed')) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Summary Approval Dashboard",
                "You accessed the summary approval dashboard. {$stats['pending']} pending approvals",
                'info',
                route('forensic.summary.approved')
            );
            session(['summary_approval_accessed' => true]);
        }

        return view('forensic_analyst.summary-approval', compact('summaries', 'stats'));
    }

    /**
     * Show approved summaries history
     */
    public function approved(Request $request)
    {
        $query = SummaryVerification::with(['complaint', 'user', 'approver'])
            ->where('status', 'approved')
            ->orderBy('updated_at', 'desc');

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('complaint', function($cq) use ($search) {
                    $cq->where('track_id', 'like', "%{$search}%")
                       ->orWhere('subject', 'like', "%{$search}%");
                });
            });
        }

        $verifiedSummaries = $query->paginate(10)->withQueryString();

        return view('forensic_analyst.approved-summary', compact('verifiedSummaries'));
    }

    /**
     * Handle summary update/approval
     */
    public function update(Request $request)
    {
        $request->validate([
            'complaint_id' => 'required|integer|exists:complaints,id',
            'approved_summary' => 'required|string|max:5000',
            'action' => 'required|in:approve,reject',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $currentUserId = Auth::id();
        $case = Complaint::find($request->complaint_id);

        // Update the original summary status
        $summary = Summary::where('complaint_id', $request->complaint_id)->first();
        $oldStatus = $summary ? $summary->status : null;
        
        if ($summary) {
            $summary->update([
                'status' => $request->action === 'approve' ? 'approved' : 'rejected'
            ]);
        }

        // Create or update verification record
        SummaryVerification::updateOrCreate(
            [
                'complaint_id' => $request->complaint_id,
                'user_id' => $summary ? $summary->user_id : null,
            ],
            [
                'summary_text' => $request->approved_summary,
                'approved_by' => $currentUserId,
                'status' => $request->action === 'approve' ? 'approved' : 'rejected',
            ]
        );

        // ✅ **NEW: Summary approval/rejection notification**
        $actionText = $request->action === 'approve' ? 'approved' : 'rejected';
        NotificationHelper::createForUser(
            $currentUserId,
            "Summary {$actionText}",
            "You {$actionText} summary for case #{$case->track_id}",
            $request->action === 'approve' ? 'success' : 'warning',
            route('forensic.summary.approved')
        );

        // ✅ **NEW: Notify summary creator about approval/rejection
        if ($summary && $summary->user_id && $summary->user_id != $currentUserId) {
            $notificationMessage = $request->action === 'approve' 
                ? "Your summary for case #{$case->track_id} has been approved"
                : "Your summary for case #{$case->track_id} has been rejected. Please review and resubmit.";
            
            NotificationHelper::createForUser(
                $summary->user_id,
                "Summary {$actionText}",
                $notificationMessage,
                $request->action === 'approve' ? 'success' : 'danger',
                route('forensic.summary.approved')
            );
        }

        // ✅ **NEW: Notify admin about summary action
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Summary {$actionText}",
                "Forensic analyst {$actionText} summary for case #{$case->track_id}",
                'info',
                route('admin.complaints.show', $request->complaint_id)
            );
        }

        // Log activity
        RecentActivities::create([
            'user_id' => $currentUserId,
            'action'  => "Summary {$actionText} for case #{$case->track_id}",
        ]);

        return back()->with('success', 
            $request->action === 'approve' 
            ? 'Summary approved successfully!' 
            : 'Summary rejected successfully!'
        );
    }

    /**
     * View specific summary details
     */
    public function show($complaintId)
    {
        $summary = Summary::with(['complaint', 'user'])
            ->where('complaint_id', $complaintId)
            ->firstOrFail();

        $verification = SummaryVerification::with(['approver'])
            ->where('complaint_id', $complaintId)
            ->first();

        // ✅ **NEW: Summary details view notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Summary Details Viewed",
            "You viewed summary details for case #{$summary->complaint->track_id}",
            'info',
            route('forensic.summary.detail', $complaintId)
        );

        return view('forensic_analyst.partials.summary-detail', compact('summary', 'verification'));
    }
}