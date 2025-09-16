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
        //generate track id
         $lastComplaint = Complaint::orderByDesc('id')->first();

    if ($lastComplaint && preg_match('/(\d{6})$/', $lastComplaint->track_id, $matches)) {
        $lastNumber = (int)$matches[1];
    } else {
        $lastNumber = 0;
    }

    $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

    $trackId = 'CT-' . date('Y') . '-' . $nextNumber;

        // Validate input
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'incident_type' => 'nullable|string|max:100',
            'severity' => 'nullable|string|max:50',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,mp3,wav,aac,pdf,doc,docx,txt,xlsx,pptx,zip,rar|max:10240'

        ],
    [
        
    ]);

        // 1. Save complaint
        $complaint = Complaint::create([
            'user_id' => Auth::id(),
            'track_id' => $trackId,
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
    $path = $file->store('media_uploads', 'public');

    // Detect file type from extension
    $extension = strtolower($file->getClientOriginalExtension());

    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $fileType = 'image';
    } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'mkv'])) {
        $fileType = 'video';
    } elseif (in_array($extension, ['mp3', 'wav', 'aac'])) {
        $fileType = 'audio';
    } elseif (in_array($extension, ['zip', 'rar'])) {
        $fileType = 'archive';
    } else {
        $fileType = 'document'; // pdf, doc, docx, txt, xlsx sab is me aayenge
    }

    Media::create([
        'user_id'   => Auth::id(),
        'complaint_id' => $complaint->id,
        'file_type' => $fileType,
        'file_path' => $path,
        'status'    => 'pending',
    ]);
}


        return redirect()->route('public.dashboard')->with('success', 'Complaint submitted successfully!. Your Track ID: ' . $trackId);
    }
}
