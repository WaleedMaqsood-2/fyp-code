<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendingSummaries;
use App\Models\RecentActivities; // Add this if not exists
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = PendingSummaries::with(['complaint', 'generatedBy', 'approvedBy'])
                        ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $summaries = $query->paginate(10)->withQueryString();

        // ✅ **NEW: Summary dashboard access notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Summary Approvals",
            "You accessed the summary approval dashboard",
            'info',
            route('admin.summaries')
        );

        return view('admin.summaries', compact('summaries', 'status'));
    }

    public function approve($id)
    {
        $summary = PendingSummaries::findOrFail($id);
        $oldStatus = $summary->status;
        
        $summary->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        // ✅ **NEW: Summary approval notification to admin**
        NotificationHelper::createForUser(
            Auth::id(),
            "Summary Approved",
            "You approved summary #{$id} for complaint #{$summary->complaint->track_id}",
            'success',
            route('admin.summaries')
        );

        // ✅ **NEW: Summary approval notification to generator (forensic analyst)
        if ($summary->generated_by) {
            NotificationHelper::createForUser(
                $summary->generated_by,
                "Summary Approved",
                "Your summary for complaint #{$summary->complaint->track_id} has been approved",
                'success',
                route('forensic.summaries.show', $summary->id)
            );
        }

        // ✅ **NEW: Summary approval notification to complaint owner
        if ($summary->complaint->user_id) {
            NotificationHelper::createForUser(
                $summary->complaint->user_id,
                "Forensic Summary Approved",
                "Forensic summary for your complaint #{$summary->complaint->track_id} has been approved",
                'info',
                route('public.complaints.track', ['track_id' => $summary->complaint->track_id])
            );
        }

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => "Summary #{$id} approved for complaint {$summary->complaint->track_id}",
        ]);

        return back()->with('success', 'Summary approved successfully.');
    }

    public function reject($id)
    {
        $summary = PendingSummaries::findOrFail($id);
        $oldStatus = $summary->status;
        
        $summary->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
        ]);

        // ✅ **NEW: Summary rejection notification to admin**
        NotificationHelper::createForUser(
            Auth::id(),
            "Summary Rejected",
            "You rejected summary #{$id} for complaint #{$summary->complaint->track_id}",
            'warning',
            route('admin.summaries')
        );

        // ✅ **NEW: Summary rejection notification to generator (forensic analyst)
        if ($summary->generated_by) {
            NotificationHelper::createForUser(
                $summary->generated_by,
                "Summary Rejected",
                "Your summary for complaint #{$summary->complaint->track_id} has been rejected. Please review and resubmit.",
                'danger',
                route('forensic.summaries.edit', $summary->id)
            );
        }

        // ✅ **NEW: Summary rejection notification to complaint owner (optional)
        if ($summary->complaint->user_id) {
            NotificationHelper::createForUser(
                $summary->complaint->user_id,
                "Forensic Summary Under Review",
                "Forensic summary for your complaint #{$summary->complaint->track_id} requires additional review",
                'warning',
                route('public.complaints.track', ['track_id' => $summary->complaint->track_id])
            );
        }

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => "Summary #{$id} rejected for complaint {$summary->complaint->track_id}",
        ]);

        return back()->with('error', 'Summary rejected.');
    }
}