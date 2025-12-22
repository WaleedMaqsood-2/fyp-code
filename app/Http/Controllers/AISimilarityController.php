<?php
// app/Http/Controllers/Police/AISimilarityController.php

namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use App\Services\SimilarityService;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class AISimilarityController extends Controller
{
    protected $similarityService;
    
    public function __construct(SimilarityService $similarityService)
    {
        $this->similarityService = $similarityService;
    }
    
    /**
     * Check similarity for a specific complaint
     */
    public function checkSimilarity(Request $request, $complaintId)
    {
        try {
            $result = $this->similarityService->findSimilarComplaints($complaintId);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'complaint_id' => $complaintId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Similarity check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Similarity check failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get similarity analytics dashboard
     */
    public function getAnalytics(Request $request)
    {
        $days = $request->get('days', 30);
        $analytics = $this->similarityService->getSimilarityAnalytics($days);
        
        return response()->json([
            'success' => true,
            'analytics' => $analytics,
            'time_period' => "Last {$days} days"
        ]);
    }
    
    /**
     * Manual similarity check with custom parameters
     */
    public function manualCheck(Request $request)
    {
        $request->validate([
            'complaint_id' => 'required|exists:complaints,id',
            'threshold' => 'numeric|min:0|max:100',
            'limit' => 'integer|min:1|max:50'
        ]);
        
        $complaintId = $request->complaint_id;
        $threshold = $request->threshold ?? 75;
        $limit = $request->limit ?? 10;
        
        // Temporarily adjust threshold
        $this->similarityService->threshold = $threshold / 100;
        
        $result = $this->similarityService->findSimilarComplaints($complaintId, $limit);
        
        return response()->json([
            'success' => true,
            'parameters' => [
                'threshold' => $threshold,
                'limit' => $limit
            ],
            'result' => $result
        ]);
    }
    
    /**
     * Train/update embeddings for all complaints
     */
    public function updateAllEmbeddings(Request $request)
    {
        try {
            $count = 0;
            $complaints = Complaint::whereNull('embedding')
                ->orWhere('embedding', '')
                ->chunk(100, function ($chunk) use (&$count) {
                    foreach ($chunk as $complaint) {
                        $this->updateEmbedding($complaint);
                        $count++;
                    }
                });
            
            return response()->json([
                'success' => true,
                'message' => "Embeddings updated for {$count} complaints"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Embedding update failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    private function updateEmbedding($complaint)
    {
        try {
            $response = Http::post(env('AI_SIMILARITY_URL') . '/embed', [
                'text' => $complaint->description
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $complaint->embedding = json_encode($data['embedding']);
                $complaint->text_hash = hash('sha256', $complaint->description);
                $complaint->save();
            }
        } catch (\Exception $e) {
            Log::error("Failed to update embedding for complaint {$complaint->id}: " . $e->getMessage());
        }
    }
}