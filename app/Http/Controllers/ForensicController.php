<?php

namespace App\Http\Controllers\Forensic;

use App\Models\Complaint;
use App\Models\ForensicReview;
use Illuminate\Routing\Controller;
use App\Services\TranscriptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Transcription;
use App\Models\TranscriptionVerification;
class ForensicController extends Controller
{
    protected $transcriptionService;
    
    public function __construct(TranscriptionService $transcriptionService)
    {
        $this->middleware('role:forensic');
        $this->transcriptionService = $transcriptionService;
    }
    
    // Pending verifications list
    public function pendingVerifications()
    {
        $assignments = ForensicReview::with(['case', 'transcription'])
            ->where('analyst_id', auth()->id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('forensic.verifications.index', compact('assignments'));
    }
    
    // Verify transcription
    public function verify(Request $request, $transcriptionId)
    {
        $transcription = Transcription::with('verifications')->findOrFail($transcriptionId);
        
        // Check if assigned to this analyst
        $assignment = ForensicReview::where('transcription_id', $transcriptionId)
            ->where('analyst_id', auth()->id())
            ->first();
        
        if (!$assignment) {
            abort(403, 'This transcription is not assigned to you');
        }
        
        $request->validate([
            'corrected_text' => 'required_without:corrected_roman|string|nullable',
            'corrected_roman' => 'required_without:corrected_text|string|nullable',
            'approved' => 'boolean',
            'notes' => 'nullable|string',
            'confidence_level' => 'nullable|in:low,medium,high'
        ]);
        
        $result = $this->transcriptionService->verifyTranscription(
            $transcriptionId,
            Auth::id(),
            [
                'corrected_text' => $request->corrected_text,
                'corrected_roman' => $request->corrected_roman,
                'approved' => $request->approved ?? true,
                'notes' => $request->notes
            ]
        );
        
        if ($result['success']) {
            // Assignment update karein
            $assignment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'confidence_level' => $request->confidence_level
            ]);
            
            // Case status update karein agar approved hai
            if ($request->approved) {
                $case = Complaint::where('transcription_id', $transcriptionId)->first();
                if ($case) {
                    $case->update(['status' => 'forensic_completed']);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Verification completed successfully',
                'next_assignment' => route('forensic.verifications.pending')
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Verification failed'
        ], 500);
    }
    
    // View verified transcriptions
    public function verifiedTranscriptions()
    {
        $verifications = TranscriptionVerification::with(['transcription', 'transcription.complaint'])
            ->where('analyst_id', Auth::id())
            ->where('approved', true)
            ->orderBy('verified_at', 'desc')
            ->paginate(10);
        
        return view('forensic.verifications.verified', compact('verifications'));
    }
    
    // Generate report from verified transcription
    public function generateReport($verificationId)
    {
        $verification = TranscriptionVerification::with(['transcription', 'complaint'])->findOrFail($verificationId);
        
        // Summary generate karein
        $summaryService = new \App\Services\SummaryService();
        $summary = $summaryService->generateFromText($verification->corrected_text);
        
        $report = \App\Models\ForensicReport::create([
            'verification_id' => $verificationId,
            'analyst_id' => Auth::id(),
            'summary' => $summary,
            'findings' => $verification->notes,
            'status' => 'draft'
        ]);
        
        return response()->json([
            'success' => true,
            'report_id' => $report->id,
            'summary' => $summary,
            'download_url' => route('forensic.report.download', $report->id)
        ]);
    }
}