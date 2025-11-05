<?php
namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\User;
use App\Models\ComplaintStatusLog;
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
         // ->whereIn('status', ['received', 'under_review'])
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

        // // ✅ Fetch forwarded cases log
        // $forwardedCases = ComplaintStatusLog::with(['complaint', 'forwardedUser'])
        //     ->where('status', 'forwarded')
        //     ->where('police_officer', $officerId)
        //     ->orderBy('created_at', 'desc')
        //     ->get()
        //     ->map(function ($log) {
        //         return [
        //             'case_id' => $log->complaint_id,
        //             'analyst_name' => $log->forwardedUser?->name ?? 'N/A',
        //             'remarks' => $log->note ?? '—',
        //             'analysis_status' => ucfirst($log->status),
        //             'created_at' => $log->created_at,
        //         ];
        //     });

            
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

        // ✅ Update complaint status
        // $complaint->update([
        //     'status' => 'forwarded',
        // ]);

        // ✅ Log forwarding in complaint_status_logs
        ComplaintStatusLog::create([
            'complaint_id' => $complaint->id,
            'police_officer' => Auth::id(),                // police officer
            'forwarded_to' => $request->analyst_id, // forensic analyst
            'status' => 'forwarded',
            'note' => $request->remarks,
            'changed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Case successfully forwarded to the Forensic Analyst.');
    }
}
