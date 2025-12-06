<?php

namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use App\Services\TranscriptionService;
use Illuminate\Http\Request;

class PoliceController extends Controller
{
    protected $transcriptionService;
    
    public function __construct(TranscriptionService $transcriptionService)
    {
        $this->middleware('role:police');
        $this->transcriptionService = $transcriptionService;
    }
    
    // Record FIR via voice
    public function recordFIR(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a,ogg|max:10240',
            'complaint_id' => 'required|exists:complaints,id',
            'case_type' => 'required|string',
            'priority' => 'required|in:low,medium,high'
        ]);
        
        $result = $this->transcriptionService->processPoliceFIR(
            $request->file('audio'),
            $request->complaint_id,
            auth()->id()
        );
        
        if ($result['success']) {
            // Case record update karein
            $case = \App\Models\CaseRecord::create([
                'complaint_id' => $request->complaint_id,
                'officer_id' => auth()->id(),
                'case_type' => $request->case_type,
                'priority' => $request->priority,
                'transcription_id' => $result['data']['transcription_id'],
                'summary' => $result['data']['summary'],
                'status' => 'under_investigation'
            ]);
            
            // Forensic analysts ko assign karein
            $this->assignToForensic($case->id, $result['data']['transcription_id']);
            
            return response()->json([
                'success' => true,
                'message' => 'FIR recorded successfully',
                'case_id' => $case->id,
                'transcription_id' => $result['data']['transcription_id'],
                'summary' => $result['data']['summary']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'FIR recording failed'
        ], 500);
    }
    
    // View assigned cases with transcriptions
    public function myCases()
    {
        $cases = \App\Models\CaseRecord::with(['complaint', 'transcription'])
            ->where('officer_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('police.cases.index', compact('cases'));
    }
    
    // Edit transcription (if needed)
    public function editTranscription(Request $request, $transcriptionId)
    {
        $transcription = Transcription::findOrFail($transcriptionId);
        
        // Check permission
        $case = \App\Models\CaseRecord::where('transcription_id', $transcriptionId)
            ->where('officer_id', auth()->id())
            ->first();
        
        if (!$case) {
            abort(403, 'You are not authorized to edit this transcription');
        }
        
        $request->validate([
            'edited_text' => 'required|string',
            'is_roman' => 'boolean'
        ]);
        
        if ($request->is_roman) {
            $transcription->roman_text = $request->edited_text;
        } else {
            $transcription->original_text = $request->edited_text;
            $transcription->transcript = $request->edited_text;
        }
        
        $transcription->save();
        
        // New verification required
        TranscriptionVerification::create([
            'transcription_id' => $transcriptionId,
            'complaint_id' => $transcription->complaint_id,
            'analyst_id' => null,
            'corrected_text' => $transcription->original_text,
            'corrected_roman' => $transcription->roman_text,
            'approved' => false,
            'notes' => 'Edited by police officer',
            'verified_at' => null
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Transcription updated. Sent for re-verification.'
        ]);
    }
    
    private function assignToForensic($caseId, $transcriptionId)
    {
        // Forensic analysts ko automatically assign karein
        $analysts = \App\Models\User::where('role', 'forensic')
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(2)
            ->get();
        
        foreach ($analysts as $analyst) {
            \App\Models\ForensicAssignment::create([
                'case_id' => $caseId,
                'analyst_id' => $analyst->id,
                'transcription_id' => $transcriptionId,
                'assigned_by' => auth()->id(),
                'status' => 'pending'
            ]);
        }
    }
}