<?php

namespace App\Http\Controllers\ForensicController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;    
use App\Models\Complaint;
use App\Models\RecentActivities; // Add this if not exists
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AssignedCasesController extends Controller
{
    public function assignedCases(Request $request)
    {
        $analystId = Auth::id();

        $query = Complaint::with('officer')
            ->whereHas('latestStatus', function ($q) use ($analystId) {
                $q->where('forwarded_to', $analystId)
                  ->where('status', 'forwarded');
            });

        // Search
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(id) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(track_id) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(subject) like ?', ["%{$search}%"])
                  ->orWhereHas('officer', function($q2) use ($search) {
                      $q2->whereRaw('LOWER(name) like ?', ["%{$search}%"]);
                  });
            });
            
            // ✅ **NEW: Search activity notification**
            NotificationHelper::createForUser(
                $analystId,
                "Case Search",
                "You searched for cases with keyword: '{$request->search}'",
                'info',
                route('forensic.assigned-cases')
            );
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('severity', $request->priority);
        }

        // Get total count for the button
        $totalCases = $query->count();

        // Check if show_all is requested
        if ($request->filled('show_all')) {
            // Get ALL cases without pagination
            $assignedCases = $query->orderBy('created_at','desc')->get();
            
            // ✅ **NEW: Show all cases notification**
            if (!session()->has('all_cases_shown')) {
                NotificationHelper::createForUser(
                    $analystId,
                    "All Cases View",
                    "You are viewing all {$totalCases} assigned cases",
                    'info',
                    route('forensic.assigned-cases', ['show_all' => true])
                );
                session(['all_cases_shown' => true]);
            }
            
            // Return special view for "Show All"
            if($request->ajax()) {
                return view('forensic_analyst.partials.all-cases-table', compact('assignedCases', 'totalCases'))->render();
            }
        } else {
            // Get paginated results
            $assignedCases = $query->orderBy('created_at','desc')->paginate(4)->withQueryString();
        }

        $statuses = Complaint::pluck('status')->unique();
        $severties = Complaint::pluck('severity')->unique();

        // ✅ **NEW: Assigned cases dashboard access notification**
        if (!session()->has('assigned_cases_accessed')) {
            NotificationHelper::createForUser(
                $analystId,
                "Assigned Cases",
                "You accessed your assigned cases dashboard. Total: {$totalCases} cases",
                'info',
                route('forensic.assigned-cases')
            );
            session(['assigned_cases_accessed' => true]);
        }

        // AJAX request returns partial table
        if ($request->ajax()) {
            return view('forensic_analyst.partials.search-assigned-cases-table', compact('assignedCases', 'totalCases'))->render();
        }

        return view('forensic_analyst.assigned-cases', compact('assignedCases','statuses','severties', 'totalCases'));
    }

    public function showReport($id)
    {
        $case = Complaint::with('user')->findOrFail($id);
        $evidences = $case->media ?? collect();
        
        // ✅ **NEW: Report view notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Forensic Report Viewed",
            "You viewed forensic report for case #{$case->track_id}",
            'info',
            route('forensic.report', $id)
        );

        return view('forensic_analyst.ai-report', compact('case', 'evidences'));
    }

    public function exportReport($id)
    {
        // Fetch case with relations
        $case = Complaint::with('user')->findOrFail($id);
        $evidences = $case->media ?? collect();

        // ✅ **NEW: Report export notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Forensic Report Exported",
            "You exported PDF report for case #{$case->track_id}",
            'success',
            route('forensic.assigned-cases')
        );

        // ✅ **NEW: Notify admin about report export
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Forensic Report Exported",
                "Forensic analyst exported report for case #{$case->track_id}",
                'info',
                route('admin.complaints.show', $id)
            );
        }

        // Load Blade into PDF
        $pdf = Pdf::loadView('forensic_analyst.partials.report-pdf', [
            'case' => $case,
            'evidences' => $evidences
        ]);

        // Return downloadable file
        return $pdf->download('AI_Forensic_Report_Case_' . $case->id . '.pdf');
    }
}