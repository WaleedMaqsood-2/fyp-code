<?php

namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
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
    ->limit(10) // 👈 Show only top 10 incident types
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

        return view('police.dashboard', compact('cases', 'viewAll', 'chartData'));
    }

    public function update(Request $request, $id)
    {
        $case = Complaint::findOrFail($id);

        $case->update([
            'status' => $request->status,
            'note' => $request->note,
        ]);

        return redirect()->route('police.dashboard')->with('success', 'Case updated successfully.');
    }
}
