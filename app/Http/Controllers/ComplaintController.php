<?php
namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'incident_type' => 'nullable|string|max:100',
            'severity' => 'nullable|string|max:50',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,pdf|max:10240'
        ],
    [
        
    ]);

        // 1. Save complaint
        $complaint = Complaint::create([
            'user_id' => Auth::id(),
            'description' => $request->description,
            'status' => 'received',
            'subject' => $request->subject,
            'location' => $request->location,
            'incident_type' => $request->incident_type,
            'severity' => $request->severity,
            
        ]);

        // 2. Save media if file uploaded
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $path = $file->store('complaints', 'public');

            Media::create([
                'user_id' => Auth::id(),
                'complaint_id' => $complaint->id,
                'file_type' => $file->getClientOriginalExtension(), // image/video/document etc detect krna hoga
                'file_path' => $path,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('public.dashboard')->with('success', 'Complaint submitted successfully!');
    }
}
