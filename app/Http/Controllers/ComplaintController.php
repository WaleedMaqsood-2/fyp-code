<?php
namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\Complaint;
use App\Models\Media;
use App\Models\RecentActivities;
use App\Models\Transcription;
use App\Services\TranscriptionService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    protected $transcriptionService;
    
    public function __construct(TranscriptionService $transcriptionService)
    {
        $this->transcriptionService = $transcriptionService;
    }

    public function create()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $complaints = Complaint::with(['user', 'assignedUser'])
                ->where('user_id', $user->id)
                ->where('is_visible_to_user', 1)
                ->latest()
                ->get();
        } else {
            $complaints = collect();
        }

        return view('public_user.complaints-form', compact('complaints'));
    }

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

        // Validate input (audio add karein)
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'incident_type' => 'nullable|string|max:100',
            'severity' => 'nullable|string|max:50',
            'evidence' => 'nullable|array',
            'evidence.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,mp3,wav,aac,pdf,doc,docx,txt,xlsx,pptx,zip,rar|max:10240',
            'voice_complaint' => 'nullable|file|mimes:mp3,wav,m4a,ogg|max:5120', // Voice complaint field add karein
        ]);

        $userId = Auth::check() ? Auth::id() : null;

        // Save complaint
        $complaint = Complaint::create([
            'user_id' => $userId,
            'track_id' => $trackId,
            'description' => $request->description,
            'status' => 'received',
            'subject' => $request->subject,
            'location' => $request->location,
            'incident_type' => $request->incident_type,
            'severity' => $request->severity,
            'complaint_type' => $request->has('voice_complaint') ? 'voice' : 'text', // Type add karein
        ]);

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => Auth::user()->name.' submitted a new complaint.',
        ]);

        // **VOICE COMPLAINT PROCESSING**
        if ($request->hasFile('voice_complaint')) {
            try {
                // Voice complaint ka transcription karein
                $result = $this->transcriptionService->processPublicComplaint(
                    $request->file('voice_complaint'),
                    $complaint->id,
                    $userId ? false : true // Anonymous check
                );

                if ($result['success']) {
                    // Complaint description update karein transcription se
                    $complaint->update([
                        'description' => $result['data']['preview_text'] ?? 'Voice complaint transcribed',
                        'has_transcription' => true,
                        'transcription_id' => $result['data']['transcription_id'] ?? null
                    ]);

                    // Notification for voice complaint
                    NotificationHelper::createForUser(
                        $userId,
                        "Voice Complaint Transcribed",
                        "Your voice complaint #{$trackId} has been transcribed successfully",
                        'info',
                        route('public.complaints.track', ['track_id' => $trackId])
                    );
                } else {
                    Log::error('Voice transcription failed: ' . ($result['error'] ?? 'Unknown error'));
                }
            } catch (\Exception $e) {
                Log::error('Voice complaint processing error: ' . $e->getMessage());
            }
        }

        // Original notifications
        NotificationHelper::publicFIRSubmitted(
            $userId,
            $complaint->id,
            $trackId
        );
        
        NotificationHelper::publicCaseStatusUpdate(
            $userId,
            $complaint->id,
            'Received'
        );

        // Notify police officers
        $policeOfficers = \App\Models\User::where('role_id', 2)->active()->get();
        foreach ($policeOfficers as $police) {
            NotificationHelper::policeNewFIR(
                $police->id,
                $complaint->id,
                $trackId,
                Auth::user()->name
            );
        }

        // Notify admins
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            $message = "Complaint #{$trackId} submitted by " . Auth::user()->name;
            if ($request->hasFile('voice_complaint')) {
                $message .= " (Voice Complaint)";
            }
            
            NotificationHelper::createForUser(
                $admin->id,
                "New Complaint Filed",
                $message,
                'info',
                route('complaints.ajaxSearch', $complaint->id)
            );
        }

        // Save media if multiple files uploaded
        if ($request->hasFile('evidence')) {
            $fileCount = 0;
            foreach ($request->file('evidence') as $file) {
                if ($file->isValid()) {
                    $fileCount++;
                    $path = $file->store('media_uploads', 'public');

                    // Detect file type
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
            
            if ($fileCount > 0) {
                NotificationHelper::createForUser(
                    $userId,
                    "Evidence Uploaded",
                    "{$fileCount} files uploaded with your complaint #{$trackId}",
                    'info',
                    route('public.complaints.track', ['track_id' => $trackId])
                );
            }
        }

        return redirect()->route('public.complaints.form')
            ->with('success', 'Complaint submitted successfully! Your Track ID: ' . $trackId);
    }

    // New method for voice complaint preview
   public function previewVoiceComplaint(Request $request)
{
    Log::info('=== PREVIEW VOICE COMPLAINT STARTED ===');
    
    $request->validate([
        'voice_complaint' => 'required|file|mimes:mp3,wav,m4a,ogg|max:5120'
    ]);

    Log::info('File received: ' . $request->file('voice_complaint')->getClientOriginalName());
    Log::info('File size: ' . $request->file('voice_complaint')->getSize());
    Log::info('File MIME: ' . $request->file('voice_complaint')->getMimeType());

    try {
        $result = $this->transcriptionService->transcribeAudio(
            $request->file('voice_complaint'),
            0, // Temporary ID
            null,
            Auth::id() ?? null
        );

        Log::info('Transcription result: ' . json_encode($result));

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'preview_text' => $result['roman_text'],
                'original_text' => $result['original_text'],
                'confidence' => $result['confidence'] ?? 0
            ]);
        } else {
            Log::error('Transcription failed: ' . ($result['error'] ?? 'Unknown'));
            Log::error('File path: ' . ($result['file_path'] ?? 'Not set'));
            
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Preview generation failed',
                'debug' => 'Check laravel.log for details'
            ], 500);
        }
        
    } catch (\Exception $e) {
        Log::error('Exception in previewVoiceComplaint: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'error' => 'Internal server error: ' . $e->getMessage()
        ], 500);
    }
}

    // Check transcription status
    public function checkTranscriptionStatus($complaintId)
    {
        $complaint = Complaint::findOrFail($complaintId);
        
        if ($complaint->user_id && $complaint->user_id !== Auth::id()) {
            abort(403);
        }

        $transcription = Transcription::where('complaint_id', $complaintId)->first();
        
        if ($transcription) {
            $verification = $transcription->latestVerification;
            
            return response()->json([
                'transcription_status' => $transcription->status,
                'confidence_score' => $transcription->confidence_score,
                'original_text' => $transcription->original_text,
                'roman_text' => $transcription->roman_text,
                'is_verified' => $verification ? $verification->approved : false,
                'verified_by' => $verification && $verification->analyst ? $verification->analyst->name : null,
                'verified_at' => $verification ? $verification->verified_at->format('Y-m-d H:i:s') : null
            ]);
        }

        return response()->json([
            'transcription_status' => 'not_found',
            'message' => 'No transcription available'
        ]);
    }

    public function hide($id)
    {
        $complaint = Complaint::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $complaint->is_visible_to_user = 0;
        $complaint->save();

        // Also delete related transcription if exists
        $transcription = Transcription::where('complaint_id', $id)->first();
        if ($transcription) {
            $transcription->delete();
        }

        NotificationHelper::createForUser(
            Auth::id(),
            "Complaint Removed",
            "Complaint #{$complaint->track_id} has been removed from your view",
            'warning',
            route('public.complaints.form')
        );

        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Complaint Hidden by User",
                "Complaint #{$complaint->track_id} was hidden by " . Auth::user()->name,
                'warning',
                route('admin.complaints.ajaxSearch', $complaint->id)
            );
        }

        return back()->with('success', 'Complaint deleted successfully.');
    }
    
    // New method to get transcription for a complaint
    public function getTranscription($complaintId)
    {
        $complaint = Complaint::findOrFail($complaintId);
        
        // Permission check
        if (Auth::user()->role_id != 1 && // Admin
            Auth::user()->role_id != 2 && // Police
            Auth::user()->role_id != 3 && // Forensic
            $complaint->user_id != Auth::id()) {
            abort(403);
        }
        
        $transcription = Transcription::where('complaint_id', $complaintId)->first();
        
        if (!$transcription) {
            return response()->json([
                'success' => false,
                'message' => 'No transcription found'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'transcription' => $transcription,
            'verification' => $transcription->latestVerification
        ]);
    }
}