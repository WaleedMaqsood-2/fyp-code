<?php
namespace App\Http\Controllers;

use App\Helpers\NotificationHelper; // Add this
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ComplaintTrackController extends Controller
{
    public function index()
    {
        $complaints = Complaint::where('is_visible_to_user', 1)->get();
        return view('public_user.complaints-track', compact('complaints'));
    }

    public function track(Request $request)
    {
        $request->validate([
            'track_id' => 'required|string'
        ]);

        $complaint = Complaint::where('track_id', $request->track_id)
            ->where('is_visible_to_user', 1)
            ->first();

        if (Complaint::where('is_visible_to_user', 0)
            ->where('track_id', $request->track_id)->exists()) {
            return back()->withErrors(['track_id' => 'Complaint deleted against this tracking number.']);
        }
        
        if (!$complaint) {
            // ✅ **NEW: Failed tracking attempt notification (for security)**
            if (Auth::check()) {
                NotificationHelper::createForUser(
                    Auth::id(),
                    "Invalid Tracking Attempt",
                    "You attempted to track invalid complaint ID: {$request->track_id}",
                    'warning',
                    route('public.complaints.track')
                );
            }
            
            return back()->withErrors(['track_id' => 'Complaint not found with this tracking number.']);
        }

        // ✅ **NEW: Track search notification to user**
        if (Auth::check() && Auth::id() == $complaint->user_id) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Complaint Tracked",
                "You searched for your complaint #{$complaint->track_id}",
                'info',
                route('public.complaints.track', ['track_id' => $complaint->track_id])
            );
        } 
        // ✅ **NEW: Track search by others (admin/police) notification**
        elseif (Auth::check() && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Complaint Viewed",
                "You viewed complaint #{$complaint->track_id} submitted by {$complaint->user->name}",
                'info',
                route('admin.complaints.ajaxSearch', $complaint->id)
            );
            
            // Notify complaint owner that their complaint was viewed
            NotificationHelper::createForUser(
                $complaint->user_id,
                "Complaint Accessed",
                "Your complaint #{$complaint->track_id} was viewed by " . Auth::user()->name,
                'info',
                route('public.complaints.track', ['track_id' => $complaint->track_id])
            );
        }

        return view('public_user.complaints-track', compact('complaint'));
    }
}