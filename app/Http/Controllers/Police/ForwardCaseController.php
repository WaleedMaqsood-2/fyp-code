<?php
namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\User;
use App\Models\ComplaintStatusLog;
use App\Models\RecentActivities; // Add this
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Support\Facades\Auth;

class ForwardCaseController extends Controller
{
    /**
     * Show Forward Case Page
     */
    public function index()
    {
        $officerId = Auth::id();

        // ✅ Fetch cases assigned to this police officer only
        $cases = Complaint::with(['officer', 'latestStatus'])
            ->where('assigned_to', $officerId)
            ->get();
        

        // ✅ Fetch forensic analysts (role name check through relationship)
        $analysts = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Forensic Analyst');
        })->get();
        
        // ✅ Fetch forwarded cases log (based on complaint_status_logs)
        $forwardedCases = ComplaintStatusLog::with(['complaint', 'forwardedUser'])
            ->where('status', 'forwarded')
            ->where('police_officer', $officerId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'case_id' => $log->complaint?->id ?? 'N/A',
                    'analyst_name' => $log->forwardedUser?->name ?? 'N/A',
                    'remarks' => $log->note ?? '—',
                    'analysis_status' => ucfirst($log->status),
                    'created_at' => $log->changed_at,
                ];
            });

        // ✅ **NEW: Forward case dashboard access notification**
        if (!session()->has('forward_case_accessed')) {
            NotificationHelper::createForUser(
                $officerId,
                "Case Forwarding Dashboard",
                "You accessed the case forwarding dashboard",
                'info',
                route('police.forward-case')
            );
            session(['forward_case_accessed' => true]);
        }

        return view('police.forward-case', compact('cases', 'analysts', 'forwardedCases'));
    }

    /**
     * Handle Forward Case Request
     */
    public function forward(Request $request)
    {
        $request->validate([
            'case_id' => 'required|exists:complaints,id',
            'analyst_id' => 'required|exists:users,id',
            'remarks' => 'required|string|max:1000',
        ]);

        $complaint = Complaint::findOrFail($request->case_id);
        $analyst = User::findOrFail($request->analyst_id);
        $trackId = $complaint->track_id;

        // ✅ Update complaint status
        $complaint->update([
            'assigned_to' => $analyst->id,
            'status' => 'forwarded',
        ]);

        // ✅ Log forwarding in complaint_status_logs
        ComplaintStatusLog::create([
            'complaint_id' => $complaint->id,
            'police_officer' => Auth::id(),                // police officer
            'forwarded_to' => $request->analyst_id, // forensic analyst
            'status' => 'forwarded',
            'note' => $request->remarks,
            'changed_at' => now(),
        ]);

        // ✅ **NEW: Case forwarding notification to police officer**
        NotificationHelper::createForUser(
            Auth::id(),
            "Case Forwarded",
            "You forwarded case #{$trackId} to forensic analyst {$analyst->name}",
            'success',
            route('police.forward-case')
        );

        // ✅ **NEW: Case assignment notification to forensic analyst**
        NotificationHelper::forensicCaseAssigned(
            $analyst->id,
            $complaint->id,
            $complaint->subject,
            $complaint->severity ?? 'Normal'
        );

        // ✅ **NEW: Notify admin about case forwarding**
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Case Forwarded to Forensic",
                "Police officer " . Auth::user()->name . " forwarded case #{$trackId} to {$analyst->name}",
                'info',
                route('admin.complaints.show', $complaint->id)
            );
        }

        // ✅ **NEW: Notify complaint owner about case forwarding**
        if ($complaint->user_id) {
            NotificationHelper::publicCaseStatusUpdate(
                $complaint->user_id,
                $complaint->id,
                'Forwarded to Forensic Analysis'
            );
        }

        // Log activity
        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => "Forwarded case #{$trackId} to forensic analyst {$analyst->name}",
        ]);

        return redirect()->back()->with('success', 'Case successfully forwarded to the Forensic Analyst.');
    }
}