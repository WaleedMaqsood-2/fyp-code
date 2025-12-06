<?php

namespace App\Http\Controllers\Forensic;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TranscriptionVerificationController extends Controller
{
    // Forensic analyst ke liye pending transcriptions
    public function pendingVerifications()
    {
        $verifications = \App\Models\TranscriptionVerification::with(['transcription', 'transcription.complaint'])
            ->whereNull('analyst_id')
            ->orWhere('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('forensic.verifications.index', [
            'verifications' => $verifications
        ]);
    }
    
    // Verify transcription
    public function verifyTranscription(Request $request, $id)
    {
        $verification = \App\Models\TranscriptionVerification::findOrFail($id);
        
        $request->validate([
            'corrected_text' => 'required|string',
            'corrected_roman' => 'nullable|string',
            'is_approved' => 'boolean',
            'notes' => 'nullable|string'
        ]);
        
        // Analyst assign karein
        $verification->analyst_id = Auth::id();
        $verification->corrected_text = $request->corrected_text;
        $verification->corrected_roman = $request->corrected_roman;
        $verification->is_approved = $request->is_approved ?? true;
        $verification->notes = $request->notes;
        $verification->verified_at = now();
        $verification->save();
        
        // Original transcription update karein agar approved hai
        if ($verification->is_approved) {
            $transcription = $verification->transcription;
            $transcription->original_transcript = $request->corrected_text;
            if ($request->corrected_roman) {
                $transcription->roman_transcript = $request->corrected_roman;
            }
            $transcription->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Transcription verified successfully'
        ]);
    }
}