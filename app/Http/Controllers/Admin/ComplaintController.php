<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\RecentActivities;
use App\Models\User;
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
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
        
        // 🔎 Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
            
            // ✅ **NEW: Admin search activity notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "Complaint Search",
                "You searched for complaints with keyword: '{$search}'",
                'info',
                route('admin.complaints.index', $request->query())
            );
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
            $query->latest();
        }

        $complaints = $query->paginate(12);
        $officers = User::where('role_id', 2)->get();

        // ✅ **NEW: Dashboard access notification (first time today)**
        if (!session()->has('complaints_viewed_today')) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Complaints Management",
                "You accessed the complaints management dashboard",
                'info',
                route('admin.complaints.index')
            );
            session(['complaints_viewed_today' => true]);
        }

        return view('admin.manage-complaints', compact('complaints', 'officers'));
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'officer_id' => 'required|exists:users,id'
        ]);

        $complaint = Complaint::findOrFail($id);
        $oldOfficerId = $complaint->assigned_to;
        $complaint->assigned_to = $request->officer_id;
        $complaint->save();

        $officer = User::find($request->officer_id);
        
        // ✅ **NEW: Notification to police officer about new assignment**
        if ($officer) {
            NotificationHelper::policeNewFIR(
                $officer->id,
                $complaint->id,
                $complaint->track_id,
                $complaint->user->name ?? 'Unknown User'
            );
        }
        
        // ✅ **NEW: Notification to complaint owner**
        if ($complaint->user_id) {
            NotificationHelper::publicCaseStatusUpdate(
                $complaint->user_id,
                $complaint->id,
                'Assigned to Police Officer'
            );
        }
        
        // ✅ **NEW: Notification to admin about assignment**
        NotificationHelper::createForUser(
            Auth::id(),
            "Complaint Assigned",
            "Complaint #{$complaint->track_id} assigned to {$officer->name}",
            'success',
            route('admin.complaints.show', $complaint->id)
        );

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'Complaint ' . $complaint->track_id . ' has been assigned to officer ' . optional($complaint->assignedTo)->name . '.',
        ]);

        return redirect()->back()->with('success', 'Complaint assigned successfully.');
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);
        
        $complaint = Complaint::findOrFail($id);
        $oldStatus = $complaint->status;

        if($complaint->status == 'received') {
            if($request->status != 'under_review'){
                return redirect()->back()->with('error', 'Please assign the complaint before changing status.');
            }
            $complaint->status = $request->status;
            $complaint->save();
            
            // ✅ **NEW: Status change notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "Complaint Status Updated",
                "Complaint #{$complaint->track_id} status changed from '{$oldStatus}' to '{$request->status}'",
                'info',
                route('admin.complaints.show', $complaint->id)
            );
            
            return redirect()->back()->with('success', 'Complaint status updated.');
        }
        elseif($complaint->assigned_to == ''){
            return redirect()->back()->with('error', 'Please assign the complaint before changing status.');
        }
        else{
            $complaint->status = $request->status;
            $complaint->save();

            // ✅ **NEW: Status change notification to complaint owner**
            if ($complaint->user_id) {
                NotificationHelper::publicCaseStatusUpdate(
                    $complaint->user_id,
                    $complaint->id,
                    $request->status
                );
            }
            
            // ✅ **NEW: Status change notification to assigned officer**
            if ($complaint->assigned_to) {
                NotificationHelper::createForUser(
                    $complaint->assigned_to,
                    "Complaint Status Updated",
                    "Complaint #{$complaint->track_id} status changed to '{$request->status}'",
                    'info',
                    route('police.complaints.show', $complaint->id)
                );
            }
            
            // ✅ **NEW: Status change notification to admin**
            NotificationHelper::createForUser(
                Auth::id(),
                "Complaint Status Updated",
                "Complaint #{$complaint->track_id} status changed from '{$oldStatus}' to '{$request->status}'",
                'info',
                route('admin.complaints.show', $complaint->id)
            );

            RecentActivities::create([
                'user_id' => Auth::id(),
                'action'  => 'Complaint ' . $complaint->track_id . ' status updated to ' . $complaint->status . '.',
            ]);

            return redirect()->back()->with('success', 'Complaint status updated successfully.');
        }
    }

    public function show($id)
    {
        $complaint = Complaint::with(['user', 'media'])->findOrFail($id);
        $officers = User::where('role_id', 2)->get();
        
        // ✅ **NEW: Complaint view notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Complaint Viewed",
            "You viewed complaint #{$complaint->track_id} submitted by " . ($complaint->user->name ?? 'Unknown'),
            'info',
            route('admin.complaints.show', $complaint->id)
        );

        return view('admin.partials.complaints-details', compact('complaint', 'officers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status'     => 'nullable|string',
            'note'       => 'nullable|string',
            'officer_id' => 'nullable|exists:users,id'
        ]);

        $complaint = Complaint::findOrFail($id);
        $oldStatus = $complaint->status;
        $oldOfficerId = $complaint->assigned_to;

        // 🔹 Assign Officer logic
        if ($request->officer_id) {
            $complaint->assigned_to = $request->officer_id;
            
            // ✅ **NEW: Officer assignment notification**
            $officer = User::find($request->officer_id);
            if ($officer) {
                NotificationHelper::policeNewFIR(
                    $officer->id,
                    $complaint->id,
                    $complaint->track_id,
                    $complaint->user->name ?? 'Unknown'
                );
            }
        }

        // 🔹 Change Status logic
        if ($request->status) {
            if ($complaint->status == 'received') {
                if ($request->status != 'under_review') {
                    return redirect()->back()->with('error', 'Please assign the complaint before changing status.');
                }
                $complaint->status = $request->status;
            }
            elseif (empty($complaint->assigned_to)) {
                return redirect()->back()->with('error', 'Please assign the complaint before changing status.');
            }
            else {
                $complaint->status = $request->status;
                
                // ✅ **NEW: Status change notification to owner**
                if ($complaint->user_id) {
                    NotificationHelper::publicCaseStatusUpdate(
                        $complaint->user_id,
                        $complaint->id,
                        $request->status
                    );
                }
            }
        }

        // 🔹 Notes logic
        if ($request->note) {
            $complaint->note = $request->note;
        }

        $complaint->save();

        // ✅ **NEW: Comprehensive update notification**
        $notificationMessage = "Complaint #{$complaint->track_id} updated";
        if ($oldStatus != $complaint->status) {
            $notificationMessage .= ". Status: {$oldStatus} → {$complaint->status}";
        }
        if ($oldOfficerId != $complaint->assigned_to) {
            $newOfficer = User::find($complaint->assigned_to);
            $notificationMessage .= $newOfficer ? ". Assigned to: {$newOfficer->name}" : ". Assignment updated";
        }
        
        NotificationHelper::createForUser(
            Auth::id(),
            "Complaint Updated",
            $notificationMessage,
            'success',
            route('admin.complaints.show', $complaint->id)
        );

        return redirect()->back()->with('success', 'Complaint updated successfully.');
    }

    public function destroy($id)
    {
        $complaint = Complaint::findOrFail($id);
        $trackId = $complaint->track_id;
        
        // ✅ **NEW: Pre-deletion notification to owner**
        if ($complaint->user_id) {
            NotificationHelper::createForUser(
                $complaint->user_id,
                "Complaint Deleted",
                "Your complaint #{$trackId} has been deleted by administrator",
                'danger',
                route('public.complaints.form')
            );
        }
        
        // ✅ **NEW: Deletion notification to admin**
        NotificationHelper::createForUser(
            Auth::id(),
            "Complaint Deleted",
            "You deleted complaint #{$trackId}",
            'warning',
            route('admin.complaints.index')
        );

        $complaint->delete();
        
        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'Complaint ' . $trackId . ' has been deleted.',
        ]);

        return redirect()->route('admin.complaints.index')->with('success', 'Complaint deleted successfully.');
    }
}