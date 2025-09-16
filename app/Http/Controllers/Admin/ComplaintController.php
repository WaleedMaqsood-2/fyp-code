<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    // AJAX live search for complaints
    public function ajaxSearch(Request $request)
    {
        $query = Complaint::with('user');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%") ;
                  });
            });
        }
        
    }

 


    public function index(Request $request)
    {
        $query = Complaint::with('user');
        $complaints = $query->paginate(12);

        
        // 🔎 Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        if ($request->filled('type')) {
            $query->where('incident_type', $request->input('type'));
        }

        if ($request->filled('officer')) {
            $query->where('assigned_to', $request->input('officer'));
        }

        // 🔽 Sorting
        if ($request->filled('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        } else {
            $query->latest(); // default sort
        }

        

        // 👮 Police officers list (assuming role_id=2 = police_officer)
    
        $officers = User::where('role_id', 2)->get();

        return view('admin.manage-complaints', compact('complaints', 'officers'));
    }

   

    // 👮 Assign Complaint
    public function assign(Request $request, $id)
    {
        $request->validate([
            'officer_id' => 'required|exists:users,id'
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->assigned_to = $request->officer_id;
        $complaint->save();

        return redirect()->back()->with('success', 'Complaint assigned successfully.');
    }

    // 🔄 Change Status
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        $complaint->save();

        return redirect()->back()->with('success', 'Complaint status updated.');
    }

   // Show complaint details page
    // public function show($id)
    // {
    //     $complaint = Complaint::with(['user', 'media'])->findOrFail($id);
    //     return view('admin.partials.complaints-details', compact('complaint'));
    // }
    
public function show($id)
{
    $complaint = Complaint::with(['user', 'media'])->findOrFail($id);
    $officers = User::where('role_id', 2)->get(); // Police officers
    return view('admin.partials.complaints-details', compact('complaint', 'officers'));
}

// Update status, assign officer, and add notes
public function update(Request $request, $id)
{
    $request->validate([
        'status' => 'nullable|string',
        'note' => 'nullable|string',
        'officer_id' => 'nullable|exists:users,id'
    ]);

    $complaint = Complaint::findOrFail($id);

    if ($request->status) {
        $complaint->status = $request->status;
    }

    if ($request->officer_id) {
        $complaint->assigned_to = $request->officer_id;
    }
if ($request->note) {
        // Assuming there's a 'notes' column in complaints table
        $complaint->note = $request->note;
    }
    $complaint->save();

    // // Save note in history
    // if ($request->note) {
    //     $complaint->history()->create([
    //         'note' => $request->note,
    //         'user_id' => auth()->id(),
    //     ]);
    // }

    return redirect()->back()->with('success', 'Complaint updated successfully.');
}

// Delete complaint (already exists)
public function destroy($id)
{
    $complaint = Complaint::findOrFail($id);
    $complaint->delete();

    return redirect()->route('admin.complaints.index')->with('success', 'Complaint deleted successfully.');
}


}
