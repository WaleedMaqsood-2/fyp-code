<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintTrackController extends Controller
{
    // Form show karega
    public function index()
    {
        $complaints = Complaint::all(); // ya user-specific filtering
        return view('public_user.complaints-track', compact('complaints'));
    }

    // Tracking ID se complaint find karega
    public function track(Request $request)
    {
        $request->validate([
            'track_id' => 'required|string'
        ]);

        $complaint = Complaint::where('track_id', $request->track_id)->first();

        if (!$complaint) {
            return back()->withErrors(['track_id' => 'Complaint not found with this tracking number.']);
        }

        return view('public_user.complaints-track', compact('complaint'));
    }
}
