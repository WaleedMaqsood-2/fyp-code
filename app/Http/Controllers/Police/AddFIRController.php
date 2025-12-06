<?php

namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Media;
use App\Models\RecentActivities;
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AddFIRController extends Controller
{
    /**
     * Show the FIR creation form
     */
    public function create()
    {
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

        // Send ENUM values to view
        return view('police.add-fir', ['incidentTypes' => $enumValues]);
    }

    /**
     * Store the FIR in database
     */
    public function store(Request $request)
    {
        // Generate track id
        $lastComplaint = Complaint::orderByDesc('id')->first();

        if ($lastComplaint && preg_match('/(\d{6})$/', $lastComplaint->track_id, $matches)) {
            $lastNumber = (int)$matches[1];
        } else {
            $lastNumber = 0;
        }

        $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        $trackId = 'CT-' . date('Y') . '-' . $nextNumber;

        // ✅ Validate form inputs
        $request->validate([
            'subject' => 'required|string|max:255',
            'severity' => 'nullable|string|max:50',
            'incident_type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string',
            'incident_datetime' => 'nullable|date',
            'audio_file' => 'nullable|file|mimes:webm,mp3,wav,m4a,ogg|max:10240',
            'transcribedText' => 'nullable|string',
            'evidence.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,mp3,wav,aac,pdf,doc,docx,txt,xlsx,pptx,zip,rar|max:10240'
        ]);

        $officerId = Auth::check() ? Auth::id() : null;
        $audioPath = null;

        if ($request->hasFile('audio_file')) {
            $audioPath = $request->file('audio_file')->store('audio_recordings', 'public');
        }

        // ✅ Save FIR in database
        $fir = Complaint::create([
            'user_id' => $officerId,
            'track_id' => $trackId,
            'subject' => $request->subject,
            'incident_type' => $request->incident_type,
            'severity' => $request->severity ?? 'Medium',
            'description' => $request->description,
            'location' => $request->location,
            'incident_datetime' => $request->incident_datetime ?? now(),
            'audio_file' => $audioPath,
            'transcription' => $request->transcribedText ?? null,
            'status' => 'received',
        ]);

        // ✅ **NEW: FIR creation notification to officer**
        NotificationHelper::createForUser(
            $officerId,
            "FIR Created Successfully",
            "You filed a new FIR with Track ID: {$trackId}",
            'success',
            route('police.cases')
        );

        // ✅ **NEW: Notify admins about new FIR**
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "New FIR Filed by Police",
                "Police officer " . Auth::user()->name . " filed FIR #{$trackId}",
                'info',
                route('admin.complaints.show', $fir->id)
            );
        }

        // Also store in media_uploads table if audio file uploaded
        if ($audioPath) {
            Media::create([
                'user_id'      => $officerId,
                'track_id' => $trackId,
                'complaint_id' => $fir->id,
                'file_type'    => 'audio',
                'file_path'    => $audioPath,
                'status'       => 'pending',
            ]);
        }

        // ✅ Record officer activity
        RecentActivities::create([
            'user_id' => $officerId,
            'action'  => Auth::user()->name . ' filed a new FIR (Track ID: ' . $trackId . ').',
        ]);

        // Save media if multiple files uploaded
        $fileCount = 0;
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                if ($file->isValid()) {
                    $fileCount++;
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
                        $fileType = 'document';
                    }

                    Media::create([
                        'user_id'      => $officerId,
                        'complaint_id' => $fir->id,
                        'file_type'    => $fileType,
                        'file_path'    => $path,
                        'status'       => 'pending',
                    ]);

                    // Log upload in activity
                    RecentActivities::create([
                        'user_id' => $officerId,
                        'action'  => 'Uploaded ' . $fileType . ' evidence for FIR (' . $trackId . ').',
                    ]);
                }
            }
            
            // ✅ **NEW: Evidence upload notification**
            if ($fileCount > 0) {
                NotificationHelper::createForUser(
                    $officerId,
                    "Evidence Uploaded",
                    "You uploaded {$fileCount} evidence files with FIR #{$trackId}",
                    'info',
                    route('police.cases')
                );
            }
        }

        // ✅ **NEW: System notification about new FIR**
        NotificationHelper::broadcastToRole(
            1, // Admin role ID
            "New FIR Filed",
            "Police officer filed new FIR #{$trackId} - {$request->subject}",
            'info',
            route('admin.complaints.show', $fir->id)
        );

        return redirect()->route('police.add-fir')
            ->with('success', 'FIR filed successfully! Track ID: ' . $trackId);
    }
}