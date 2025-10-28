<?php

namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Officer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{


    public function index(Request $request)
    {
        $police = Auth::user();
   

        // Start query limited to this police officer’s cases
  $query = Complaint::where('assigned_to', $police->id);


    // 🔍 Search (by track_id, description, or title)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('track_id', 'like', "%$search%")
              ->orWhere('subject', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%");
        });
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


            return view('police.cases', compact('cases'),['incidentTypes' => $enumValues]);
    }

   
    public function update(Request $request, $id)
    {
        $case = Complaint::findOrFail($id);

        $case->update([
            'status' => $request->status,
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'Case updated successfully.');
    }
}
