<?php

namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\RecentActivities; // Add this if not exists
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Support\Facades\DB;

class PoliceDashboardController extends Controller
{
    public function index(Request $request)
    {
        $police = Auth::user();
        
        // === Check if "view all" mode is active ===
        $viewAll = $request->query('view') === 'all';

        if ($viewAll) {
            // Show all complaints assigned to this officer (no limit)
            $cases = Complaint::where('assigned_to', $police->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            // ✅ **NEW: View all cases notification**
            if (!session()->has('all_dashboard_cases_shown')) {
                $totalCases = $cases->count();
                NotificationHelper::createForUser(
                    $police->id,
                    "Viewing All Dashboard Cases",
                    "You are viewing all {$totalCases} cases on dashboard",
                    'info',
                    route('police.dashboard', ['view' => 'all'])
                );
                session(['all_dashboard_cases_shown' => true]);
            }
        } else {
            // Show only latest 5 cases (paginated)
            $cases = Complaint::where('assigned_to', $police->id)
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        }

        // === Prepare Data for Charts ===
        // Group cases by incident_type (only assigned to this officer)
        $casesByType = Complaint::select('incident_type', DB::raw('COUNT(*) as total'))
            ->where('assigned_to', $police->id)
            ->groupBy('incident_type')
            ->orderByDesc('total')
            ->limit(10) // Show only top 10 incident types
            ->pluck('total', 'incident_type');

        // Group cases by status
        $casesByStatus = Complaint::select('status', DB::raw('COUNT(*) as total'))
            ->where('assigned_to', $police->id)
            ->groupBy('status')
            ->pluck('total', 'status');

        // Convert to arrays for Chart.js
        $chartData = [
            'types' => [
                'labels' => $casesByType->keys(),
                'data' => $casesByType->values(),
            ],
            'status' => [
                'labels' => $casesByStatus->keys(),
                'data' => $casesByStatus->values(),
            ],
        ];

        // ✅ **NEW: Daily dashboard summary notification**
        if (!session()->has('police_dashboard_summary_today')) {
            $totalCases = Complaint::where('assigned_to', $police->id)->count();
            $pendingCases = Complaint::where('assigned_to', $police->id)
                ->where('status', 'pending')
                ->count();
            $urgentCases = Complaint::where('assigned_to', $police->id)
                ->where('severity', 'high')
                ->count();
            
            $message = "Good morning Officer! You have {$totalCases} total cases, {$pendingCases} pending.";
            if ($urgentCases > 0) {
                $message .= " ⚠️ {$urgentCases} urgent cases need attention!";
            }
            
            NotificationHelper::createForUser(
                $police->id,
                "Daily Police Dashboard",
                $message,
                'info',
                route('police.dashboard')
            );
            
            session(['police_dashboard_summary_today' => true]);
        }

        // ✅ **NEW: High priority alert notification**
        $highPriority = Complaint::where('assigned_to', $police->id)
            ->where('severity', 'high')
            ->whereIn('status', ['pending', 'under_review'])
            ->count();
        
        if ($highPriority > 0 && !session()->has('high_priority_police_alert')) {
            NotificationHelper::createForUser(
                $police->id,
                "⚠️ High Priority Cases Alert",
                "You have {$highPriority} high priority cases requiring immediate attention",
                'danger',
                route('police.cases')
            );
            session(['high_priority_police_alert' => true]);
        }

        return view('police.dashboard', compact('cases', 'viewAll', 'chartData'));
    }

    public function update(Request $request, $id)
    {
        $case = Complaint::findOrFail($id);
        $oldStatus = $case->status;
        $trackId = $case->track_id;

        $case->update([
            'status' => $request->status,
            'note' => $request->note,
        ]);

        // ✅ **NEW: Dashboard case update notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Dashboard Case Update",
            "You updated case #{$trackId} status to '{$request->status}' from dashboard",
            'success',
            route('police.dashboard')
        );

        // ✅ **NEW: Notify complaint owner about status change
        if ($case->user_id) {
            NotificationHelper::publicCaseStatusUpdate(
                $case->user_id,
                $case->id,
                $request->status
            );
        }

        // ✅ **NEW: If case is forwarded to forensic, notify analyst
        if ($request->status === 'forwarded' && $case->assigned_to) {
            $analyst = \App\Models\User::find($case->assigned_to);
            if ($analyst) {
                NotificationHelper::createForUser(
                    $analyst->id,
                    "New Case Assigned",
                    "Police officer assigned case #{$trackId} to you",
                    'info',
                    route('forensic.assigned-cases')
                );
            }
        }

        // Log activity
        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => "Updated case #{$trackId} status to '{$request->status}' from dashboard",
        ]);

        return redirect()->back()->with('success', 'Case updated successfully.');
    }
    
    // ✅ **NEW: Method to get daily statistics for notification**
    private function getDailyStats($policeId)
    {
        $today = now()->toDateString();
        
        return [
            'cases_today' => Complaint::where('assigned_to', $policeId)
                ->whereDate('created_at', $today)
                ->count(),
            'completed_today' => Complaint::where('assigned_to', $policeId)
                ->where('status', 'completed')
                ->whereDate('updated_at', $today)
                ->count(),
            'pending_cases' => Complaint::where('assigned_to', $policeId)
                ->where('status', 'pending')
                ->count(),
        ];
    }
}