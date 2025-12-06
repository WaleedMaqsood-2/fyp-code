<?php

namespace App\Http\Controllers\Police;

use App\Models\Transcription;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WhisperTranscriptionService;


class TranscriptionController extends Controller
{
    protected $transcriptionService;
    
    public function __construct(WhisperTranscriptionService $transcriptionService)
    {
        $this->transcriptionService = $transcriptionService;
    }
    
    // Police module: Voice complaint submit karein
    public function submitVoiceComplaint(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a,ogg|max:10240', // 10MB max
            'complaint_id' => 'required|exists:complaints,id',
            'media_id' => 'required|exists:media,id'
        ]);
        
        $audioFile = $request->file('audio');
        $complaintId = $request->complaint_id;
        $mediaId = $request->media_id;
        
        // Transcription service call karein
        $result = $this->transcriptionService->transcribeAudio(
            $audioFile, 
            $complaintId, 
            $mediaId
        );
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Voice complaint submitted successfully',
                'data' => [
                    'transcription_id' => $result['transcription']->id,
                    'original_text' => $result['original_text'],
                    'roman_text' => $result['roman_text'],
                    'preview_url' => route('police.transcription.preview', $result['transcription']->id)
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Transcription failed',
                'error' => $result['error']
            ], 500);
        }
    }
    
    // Transcription preview for police
    public function previewTranscription($id)
    {
        $transcription = Transcription::findOrFail($id);
        
        return view('police.transcription.preview', [
            'transcription' => $transcription,
            'original_text' => $transcription->original_transcript,
            'roman_text' => $transcription->roman_transcript
        ]);
    }
    
    // Edit transcription (if needed)
    public function editTranscription(Request $request, $id)
    {
        $transcription = Transcription::findOrFail($id);
        
        $request->validate([
            'edited_text' => 'required|string',
            'is_roman' => 'boolean'
        ]);
        
        if ($request->is_roman) {
            $transcription->roman_transcript = $request->edited_text;
        } else {
            $transcription->original_transcript = $request->edited_text;
        }
        
        $transcription->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Transcription updated successfully'
        ]);
    }
}