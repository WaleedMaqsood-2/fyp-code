<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintTrackController extends Controller
{
    // Form show karega
    public function index()
    {
        $complaints = Complaint::where('is_visible_to_user', 1)->get(); // ya user-specific filtering
        return view('public_user.complaints-track', compact('complaints'));
    }

    // Tracking ID se complaint find karega
    public function track(Request $request)
    {
        $request->validate([
            'track_id' => 'required|string'
        ]);

        $complaint = Complaint::where('track_id', $request->track_id)->where('is_visible_to_user', 1)->first();

        if (Complaint::where('is_visible_to_user', 0)->where('track_id', $request->track_id)->exists()) {
            return back()->withErrors(['track_id' => 'Complaint deleted against this tracking number.']);
        }
        if (!$complaint) {
            return back()->withErrors(['track_id' => 'Complaint not found with this tracking number.']);
        }

        return view('public_user.complaints-track', compact('complaint'));
    }
}
