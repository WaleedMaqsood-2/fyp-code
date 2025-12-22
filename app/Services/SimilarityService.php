<?php
// app/Services/AdvancedSimilarityService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Complaint;
use App\Models\ComplaintSimilarity;

class SimilarityService
{
    protected $baseUrl;
    public $threshold = 0.75; // 75% similarity
    
    public function __construct()
    {
        $this->baseUrl = env('AI_SIMILARITY_URL', 'http://127.0.0.1:5005');
    }
    
    /**
     * Enhanced similarity check with multiple strategies
     */
    public function findSimilarComplaints($complaintId, $topK = 5)
    {
        $currentComplaint = Complaint::find($complaintId);
        
        if (!$currentComplaint || empty($currentComplaint->description)) {
            return [
                'success' => false,
                'message' => 'Invalid complaint'
            ];
        }
        
        // Get candidates with embeddings
        $candidates = Complaint::where('id', '!=', $complaintId)
            ->whereNotNull('embedding')
            ->where('embedding', '!=', '')
            ->where('status', '!=', 'closed')
            ->orderBy('created_at', 'desc')
            ->limit(100) // Limit for performance
            ->get(['id', 'description', 'embedding', 'incident_type', 'location', 'created_at'])
            ->map(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'text' => $complaint->description,
                    'embedding' => json_decode($complaint->embedding, true),
                    'category' => $complaint->incident_type,
                    'location' => $complaint->location,
                    'date' => $complaint->created_at->format('Y-m-d')
                ];
            })
            ->toArray();
        
        if (empty($candidates)) {
            return [
                'success' => true,
                'matches' => [],
                'message' => 'No candidates for comparison'
            ];
        }
        
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/similarity', [
                'text' => $currentComplaint->description,
                'candidates' => $candidates,
                'top_k' => $topK,
                'threshold' => 0.25 // Lower threshold for initial filtering
            ]);
            
            if ($response->successful()) {
                $matches = $response->json()['matches'] ?? [];
                
                // Apply additional filters
                $filteredMatches = $this->applyBusinessRules($matches, $currentComplaint);
                
                // Save significant matches
                $this->saveSimilarityResults($complaintId, $filteredMatches);
                
                return [
                    'success' => true,
                    'matches' => $filteredMatches,
                    'total_candidates' => count($candidates),
                    'matches_found' => count($filteredMatches)
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Advanced similarity check failed: ' . $e->getMessage());
        }
        
        // Fallback: Simple keyword matching
        return $this->fallbackSimilarityCheck($currentComplaint);
    }
    
    /**
     * Apply business rules to filter matches
     */
    private function applyBusinessRules($matches, $currentComplaint)
    {
        $filtered = [];
        
        foreach ($matches as $match) {
            $score = $match['similarity'] ?? 0;
            
            // Rule 1: High similarity (> 85%) always included
            if ($score >= 85) {
                $match['confidence'] = 'high';
                $filtered[] = $match;
                continue;
            }
            
            // Rule 2: Medium similarity with same category
            if ($score >= 75) {
                $match['confidence'] = 'medium';
                $filtered[] = $match;
                continue;
            }
            
            // Rule 3: Location-based boost
            $locationMatch = $this->checkLocationSimilarity(
                $currentComplaint->location,
                $match['location'] ?? ''
            );
            
            if ($score >= 65 && $locationMatch) {
                $match['similarity'] = $score + 5; // Boost score
                $match['confidence'] = 'location_based';
                $filtered[] = $match;
            }
        }
        
        // Sort by similarity
        usort($filtered, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });
        
        return array_slice($filtered, 0, 5); // Return top 5
    }
    
    /**
     * Check location similarity
     */
    private function checkLocationSimilarity($loc1, $loc2)
    {
        if (empty($loc1) || empty($loc2)) {
            return false;
        }
        
        $loc1 = strtolower(trim($loc1));
        $loc2 = strtolower(trim($loc2));
        
        // Exact match
        if ($loc1 === $loc2) {
            return true;
        }
        
        // Partial match
        $commonWords = ['road', 'street', 'market', 'area', 'colony', 'town'];
        foreach ($commonWords as $word) {
            if (strpos($loc1, $word) !== false && strpos($loc2, $word) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Save similarity results to database
     */
    private function saveSimilarityResults($complaintId, $matches)
    {
        foreach ($matches as $match) {
            if ($match['similarity'] >= 75) {
                ComplaintSimilarity::updateOrCreate(
                    [
                        'complaint_id' => $complaintId,
                        'similar_complaint_id' => $match['id']
                    ],
                    [
                        'similarity_score' => $match['similarity'],
                        'matched_text' => substr($match['text'], 0, 500),
                        'confidence_level' => $match['confidence'] ?? 'medium',
                        'key_phrases' => json_encode($match['key_phrases'] ?? []),
                        'checked_at' => now()
                    ]
                );
            }
        }
    }
    
    /**
     * Fallback similarity check using keywords
     */
    private function fallbackSimilarityCheck($complaint)
    {
        $keywords = $this->extractKeywords($complaint->description);
        
        if (empty($keywords)) {
            return ['matches' => []];
        }
        
        $similar = Complaint::where('id', '!=', $complaint->id)
            ->where(function($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('description', 'like', "%{$keyword}%");
                }
            })
            ->limit(5)
            ->get(['id', 'description'])
            ->map(function ($item) use ($keywords) {
                $score = $this->calculateKeywordScore($item->description, $keywords);
                
                return [
                    'id' => $item->id,
                    'text' => $item->description,
                    'similarity' => $score,
                    'method' => 'keyword_fallback'
                ];
            })
            ->filter(function ($item) {
                return $item['similarity'] >= 50;
            })
            ->sortByDesc('similarity')
            ->values()
            ->toArray();
        
        return ['matches' => $similar];
    }
    
    /**
     * Extract keywords from Urdu text
     */
    private function extractKeywords($text)
    {
        // Remove common words
        $stopWords = ['میں', 'نے', 'کا', 'کی', 'کے', 'کو', 'سے', 'پر', 'ہے', 'ہیں', 'اور'];
        $words = preg_split('/\s+/', $text);
        
        // Filter and return unique keywords
        return array_unique(array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 2;
        }));
    }
    
    /**
     * Calculate keyword match score
     */
    private function calculateKeywordScore($text, $keywords)
    {
        $matches = 0;
        foreach ($keywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $matches++;
            }
        }
        
        if (count($keywords) == 0) {
            return 0;
        }
        
        return round(($matches / count($keywords)) * 100, 2);
    }
    
    /**
     * Batch similarity check for multiple complaints
     */
    public function batchSimilarityCheck($complaintIds)
    {
        $results = [];
        
        foreach ($complaintIds as $complaintId) {
            $similarity = $this->findSimilarComplaints($complaintId, 3);
            $results[$complaintId] = $similarity;
        }
        
        return $results;
    }
    
    /**
     * Get similarity analytics
     */
    public function getSimilarityAnalytics($days = 30)
    {
        $date = now()->subDays($days);
        
        $stats = ComplaintSimilarity::where('created_at', '>=', $date)
            ->selectRaw('
                COUNT(*) as total_matches,
                AVG(similarity_score) as avg_similarity,
                COUNT(DISTINCT complaint_id) as unique_complaints,
                confidence_level,
                DATE(created_at) as date
            ')
            ->groupBy('confidence_level', 'date')
            ->orderBy('date', 'desc')
            ->get();
        
        return [
            'stats' => $stats,
            'high_similarity_cases' => ComplaintSimilarity::where('similarity_score', '>=', 85)
                ->with(['complaint', 'similarComplaint'])
                ->limit(10)
                ->get()
        ];
    }
}