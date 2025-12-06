<?php

namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Officer;
use App\Models\RecentActivities; // Add this if not exists
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $police = Auth::user();

        // Start query limited to this police officer's cases
        $query = Complaint::where('assigned_to', $police->id);

        // 🔍 Search (by track_id, description, or title)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('track_id', 'like', "%$search%")
                  ->orWhere('subject', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
            
            // ✅ **NEW: Case search notification**
            NotificationHelper::createForUser(
                $police->id,
                "Case Search",
                "You searched for cases with keyword: '{$search}'",
                'info',
                route('police.cases', $request->query())
            );
        }
        
        // 🟡 Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔵 Filter by Incident Type
        if ($request->filled('type')) {
            $query->where('incident_type', $request->type);
        }

        // 📅 Filter by Date (created_at)
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 🔽 Sort order (ascending or descending)
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy('created_at', $sortOrder);

        // 🧭 Pagination or "Show All"
        if ($request->has('show_all')) {
            $cases = $query->get();
            
            // ✅ **NEW: Show all cases notification**
            if (!session()->has('all_cases_shown_police')) {
                NotificationHelper::createForUser(
                    $police->id,
                    "Viewing All Cases",
                    "You are viewing all assigned cases",
                    'info',
                    route('police.cases', ['show_all' => true])
                );
                session(['all_cases_shown_police' => true]);
            }
        } else {
            $cases = $query->paginate(5);
        }

        // Get ENUM options for incident_type directly from database schema
        $type = DB::select("SHOW COLUMNS FROM complaints WHERE Field = 'incident_type'")[0]->Type;

        // Extract the ENUM values using regex
        preg_match("/^enum\((.*)\)$/", $type, $matches);
        $enumValues = [];
        if (!empty($matches[1])) {
            $enumValues = array_map(function ($value) {
                return trim($value, "'");
            }, explode(',', $matches[1]));
        }

        // ✅ **NEW: Cases dashboard access notification**
        if (!session()->has('police_cases_accessed')) {
            $totalCases = $query->count();
            NotificationHelper::createForUser(
                $police->id,
                "Cases Dashboard",
                "You accessed your cases dashboard. Total: {$totalCases} cases",
                'info',
                route('police.cases')
            );
            session(['police_cases_accessed' => true]);
        }

        return view('police.cases', compact('cases'), ['incidentTypes' => $enumValues]);
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

        // ✅ **NEW: Case status update notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Case Status Updated",
            "You updated case #{$trackId} status from '{$oldStatus}' to '{$request->status}'",
            'success',
            route('police.cases')
        );

        // ✅ **NEW: Notify complaint owner about status change
        if ($case->user_id) {
            NotificationHelper::publicCaseStatusUpdate(
                $case->user_id,
                $case->id,
                $request->status
            );
        }

        // ✅ **NEW: Notify admin about case update
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Police Updated Case Status",
                "Police officer " . Auth::user()->name . " updated case #{$trackId} status to '{$request->status}'",
                'info',
                route('admin.complaints.show', $id)
            );
        }

        // ✅ **NEW: If case is completed, notify forensic analyst if assigned
        if ($request->status === 'completed' && $case->assigned_to) {
            NotificationHelper::createForUser(
                $case->assigned_to,
                "Case Marked as Completed",
                "Case #{$trackId} has been marked as completed by police",
                'success',
                route('forensic.case.details', $id)
            );
        }

        // Log activity
        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => "Updated case #{$trackId} status to '{$request->status}'",
        ]);

        return redirect()->back()->with('success', 'Case updated successfully.');
    }
}