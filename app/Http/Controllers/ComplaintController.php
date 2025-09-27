<?php
namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Media;
use App\Models\RecentActivities;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
   public function create()
{
    if (Auth::check()) {
        $user = Auth::user();
        $complaints = Complaint::with(['user', 'assignedUser'])
            ->where('user_id', $user->id)
            ->where('is_visible_to_user', 1) // sirf visible wali complaints show karo
            ->latest()
            ->get();

    } else {
        // ✅ Agar user login nahi hai, empty collection bhej do
        $complaints = collect();
    }

    return view('public_user.complaints-form', compact('complaints'));
}

    

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
    'evidence' => 'nullable|array',
    'evidence.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,mp3,wav,aac,pdf,doc,docx,txt,xlsx,pptx,zip,rar|max:10240'
], 
    [
        
    ]);
$userId = Auth::check() ? Auth::id() : null;
        // 1. Save complaint
        $complaint = Complaint::create([
            'user_id' => $userId,
            'track_id' => $trackId,
            'description' => $request->description,
            'status' => 'received',
            'subject' => $request->subject,
            'location' => $request->location,
            'incident_type' => $request->incident_type,
            'severity' => $request->severity,
            
        ]);

         RecentActivities::create([
        'user_id' => Auth::id(),
        'action'  => Auth::user()->name.' submit a new complaint.',
    ]);
      // 2. Save media if multiple files uploaded
if ($request->hasFile('evidence')) {
    foreach ($request->file('evidence') as $file) {
        if ($file->isValid()) {
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
                $fileType = 'document'; // pdf, doc, docx, txt, xlsx etc.
            }

            $media = Media::create([
                'user_id'      => $userId,
                'complaint_id' => $complaint->id,
                'file_type'    => $fileType,
                'file_path'    => $path,
                'status'       => 'pending',
            ]);

            RecentActivities::create([
                'user_id' => Auth::id(),
                'action'  => 'Media uploaded for ' . optional($media->complaint)->track_id . '.',
            ]);
        }

    }
}


return redirect()->route('public.complaints.form')
    ->with('success', 'Complaint submitted successfully! Your Track ID: ' . $trackId);
}



public function hide($id)
{
    $complaint = Complaint::where('id', $id)
        ->where('user_id', Auth::id()) // ensure ke sirf apni complaint delete kar sake
        ->firstOrFail();

    $complaint->is_visible_to_user = 0; // hide kar do
    $complaint->save();

    return back()->with('success', 'Complaint deleted successfully.');
}

}

