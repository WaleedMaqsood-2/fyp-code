<?php

namespace App\Http\Controllers\PublicUser;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Transcription;
use App\Services\TranscriptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PublicUserController extends Controller
{
    protected $transcriptionService;
    
    public function __construct(TranscriptionService $transcriptionService)
    {
        $this->transcriptionService = $transcriptionService;
    }
    
    // Public user voice complaint submit
    public function submitVoiceComplaint(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a,ogg|max:5120',
            'is_anonymous' => 'boolean',
            'complaint_type' => 'required|string'
        ]);
        
        // Complaint create karein
        $complaint = \App\Models\Complaint::create([
            'user_id' => $request->is_anonymous ? null : Auth::id(),
            'type' => $request->complaint_type,
            'status' => 'pending',
            'is_anonymous' => $request->is_anonymous ?? false
        ]);
        
        // Transcription process karein
        $result = $this->transcriptionService->processPublicComplaint(
            $request->file('audio'),
            $complaint->id,
            $request->is_anonymous ?? false
        );
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Complaint submitted successfully',
                'complaint_id' => $complaint->id,
                'tracking_code' => 'COMP-' . str_pad($complaint->id, 6, '0', STR_PAD_LEFT),
                'preview' => $result['data']['preview_text']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Submission failed'
        ], 500);
    }
    
    // Preview transcription before submission
    public function previewTranscription(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a,ogg|max:5120'
        ]);
        
        // Temporary transcription for preview
        $tempResult = $this->transcriptionService->transcribeAudio(
            $request->file('audio'),
            0, // Temporary complaint ID
            null,
            Auth::id() ?? null
        );
        
        if ($tempResult['success']) {
            return response()->json([
                'success' => true,
                'preview_text' => $tempResult['roman_text'],
                'original_text' => $tempResult['original_text']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'Preview generation failed'
        ], 500);
    }
    
    // Check complaint status
    public function checkStatus($complaintId)
    {
        $complaint = \App\Models\Complaint::findOrFail($complaintId);
        
        // Check if user has permission
        if ($complaint->user_id && $complaint->user_id !== Auth::id()) {
            abort(403);
        }
        
        $transcription = Transcription::where('complaint_id', $complaintId)->first();
        
        return response()->json([
            'complaint_id' => $complaint->id,
            'status' => $complaint->status,
            'transcription_status' => $transcription ? $transcription->status : 'not_found',
            'last_updated' => $complaint->updated_at->format('Y-m-d H:i:s'),
            'tracking_code' => 'COMP-' . str_pad($complaint->id, 6, '0', STR_PAD_LEFT)
        ]);
    }
}