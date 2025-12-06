<?php

namespace App\Http\Controllers\ForensicController;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Media;
use App\Models\FaceMatch;
use App\Models\RecentActivities; // Add this
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FaceMatchingController extends Controller
{
    /**
     * Show the Face Matching page with history
     */
    public function index(Request $request)
    {
        // Get search parameters
        $search = $request->input('search');
        $status = $request->input('status');
        $minConfidence = $request->input('min_confidence');
        
        // Query for face matches
        $query = FaceMatch::with(['complaint', 'media', 'analyst'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('complaint', function($cq) use ($search) {
                    $cq->where('track_id', 'like', "%{$search}%")
                       ->orWhere('subject', 'like', "%{$search}%");
                });
            });
            
            // ✅ **NEW: Face match search notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "Face Match Search",
                "You searched for face matches with keyword: '{$search}'",
                'info',
                route('forensic.face.match')
            );
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($minConfidence) {
            $query->where('confidence', '>=', $minConfidence);
        }

        $matches = $query->paginate(12)->withQueryString();

        // Get statistics
        $stats = [
            'total' => FaceMatch::count(),
            'verified' => FaceMatch::verified()->count(),
            'pending' => FaceMatch::pending()->count(),
            'high_confidence' => FaceMatch::highConfidence()->count(),
        ];

        // Get all cases with image evidence for dropdown
        $casesWithImages = Complaint::whereHas('media', function($q) {
            $q->whereIn('file_type', ['image', 'jpg', 'jpeg', 'png', 'gif']);
        })->with('media')->get();

        // ✅ **NEW: Face matching dashboard access notification**
        if (!session()->has('face_matching_accessed')) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Face Matching Dashboard",
                "You accessed the face matching dashboard",
                'info',
                route('forensic.face.match')
            );
            session(['face_matching_accessed' => true]);
        }

        return view('forensic_analyst.face-matching', compact('matches', 'stats', 'casesWithImages'));
    }

    /**
     * Handle face image upload and matching
     */
    public function match(Request $request)
    {
        $request->validate([
            'face_image' => 'required|image|mimes:jpg,jpeg,png,gif|max:5120',
            'complaint_id' => 'nullable|exists:complaints,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get current analyst
        $analystId = Auth::id();

        // Save uploaded reference image
        if ($request->hasFile('face_image')) {
            $image = $request->file('face_image');
            $filename = 'ref_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = 'face_matches/reference/' . $filename;
            
            // Resize and save image
            $img = Image::make($image->getRealPath())->fit(500, 500)->encode('jpg', 90);
            Storage::disk('public')->put($path, (string) $img);
            
            $referenceImagePath = $path;
        }

        // Get all image evidence from database for matching
        $allImages = Media::whereIn('file_type', ['image', 'jpg', 'jpeg', 'png', 'gif'])
            ->with('complaint')
            ->get();

        // Simulate AI face matching
        $matches = [];
        foreach ($allImages as $image) {
            // Simulate confidence score (40% to 95%)
            $confidence = rand(40, 95);
            
            // Only include matches with confidence > 60% for demo
            if ($confidence > 60) {
                $matches[] = [
                    'complaint_id' => $image->complaint_id,
                    'media_id' => $image->id,
                    'reference_image_path' => $referenceImagePath,
                    'matched_image_path' => $image->file_path,
                    'confidence' => $confidence,
                    'match_details' => [
                        'similarity_score' => $confidence,
                        'matched_features' => ['eyes', 'nose', 'mouth'],
                        'algorithm' => 'DeepFace',
                        'version' => '1.0'
                    ],
                    'analyst_id' => $analystId,
                    'status' => 'pending',
                    'notes' => $request->notes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Limit to top 6 matches for display
        usort($matches, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });
        $topMatches = array_slice($matches, 0, 6);

        // Save matches to database
        if (!empty($topMatches)) {
            FaceMatch::insert($topMatches);
        }

        // Get saved matches for display
        $savedMatches = FaceMatch::with(['complaint', 'media'])
            ->where('reference_image_path', $referenceImagePath)
            ->orderBy('confidence', 'desc')
            ->get();

        // ✅ **NEW: Successful face matching notification**
        NotificationHelper::createForUser(
            $analystId,
            "Face Matching Complete",
            "Face matching completed. " . count($savedMatches) . " potential matches found.",
            'success',
            route('forensic.face.match')
        );

        // ✅ **NEW: Notify admin about face matching results
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Face Matching Results",
                "Forensic analyst completed face matching. Found " . count($savedMatches) . " matches.",
                'info',
                route('admin.complaints.index')
            );
        }

        // Log activity
        RecentActivities::create([
            'user_id' => $analystId,
            'action'  => 'Face matching completed. Found ' . count($savedMatches) . ' matches.',
        ]);

        return back()->with([
            'success' => 'Face matching completed. ' . count($savedMatches) . ' potential matches found.',
            'new_matches' => $savedMatches
        ]);
    }

    /**
     * Verify a face match
     */
    public function verifyMatch($id)
    {
        $match = FaceMatch::findOrFail($id);
        $oldStatus = $match->status;
        
        $match->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        // ✅ **NEW: Match verification notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Face Match Verified",
            "You verified face match #{$id} with confidence {$match->confidence}%",
            'success',
            route('forensic.face.match')
        );

        // ✅ **NEW: Notify relevant parties about verified match
        if ($match->complaint_id) {
            // Notify police officer assigned to case
            $complaint = Complaint::find($match->complaint_id);
            if ($complaint && $complaint->assigned_to) {
                NotificationHelper::createForUser(
                    $complaint->assigned_to,
                    "Face Match Verified",
                    "Forensic analyst verified face match for case #{$complaint->track_id}",
                    'info',
                    route('police.cases.show', $complaint->id)
                );
            }
        }

        return back()->with('success', 'Face match verified successfully.');
    }

    /**
     * Reject a face match
     */
    public function rejectMatch($id)
    {
        $match = FaceMatch::findOrFail($id);
        $oldStatus = $match->status;
        
        $match->update([
            'status' => 'rejected',
        ]);

        // ✅ **NEW: Match rejection notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Face Match Rejected",
            "You rejected face match #{$id} with confidence {$match->confidence}%",
            'warning',
            route('forensic.face.match')
        );

        return back()->with('success', 'Face match rejected.');
    }

    /**
     * Delete a face match
     */
    public function deleteMatch($id)
    {
        $match = FaceMatch::findOrFail($id);
        $confidence = $match->confidence;
        
        // Delete reference image if no other matches use it
        $otherMatches = FaceMatch::where('reference_image_path', $match->reference_image_path)
            ->where('id', '!=', $id)
            ->count();
            
        if ($otherMatches == 0) {
            Storage::disk('public')->delete($match->reference_image_path);
        }
        
        $match->delete();

        // ✅ **NEW: Match deletion notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Face Match Deleted",
            "You deleted face match #{$id} with confidence {$confidence}%",
            'warning',
            route('forensic.face.match')
        );

        return back()->with('success', 'Face match deleted successfully.');
    }

    /**
     * View match details
     */
    public function viewMatch($id)
    {
        $match = FaceMatch::with(['complaint', 'media', 'analyst'])
            ->findOrFail($id);

        // ✅ **NEW: Match details view notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Face Match Details Viewed",
            "You viewed details for face match #{$id}",
            'info',
            route('forensic.face.match', $id)
        );

        return view('forensic_analyst.face-match-details', compact('match'));
    }

    /**
     * Get match statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total_matches' => FaceMatch::count(),
            'verified_matches' => FaceMatch::verified()->count(),
            'pending_matches' => FaceMatch::pending()->count(),
            'average_confidence' => FaceMatch::avg('confidence'),
            'high_confidence_matches' => FaceMatch::where('confidence', '>=', 80)->count(),
            'recent_matches' => FaceMatch::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get matches for a specific case
     */
    public function getCaseMatches($caseId)
    {
        $matches = FaceMatch::with(['media', 'analyst'])
            ->where('complaint_id', $caseId)
            ->orderBy('confidence', 'desc')
            ->get();

        return response()->json($matches);
    }

    /**
     * Batch verify multiple matches
     */
    public function batchVerify(Request $request)
    {
        $request->validate([
            'match_ids' => 'required|array',
            'match_ids.*' => 'exists:face_matches,id',
        ]);

        $count = count($request->match_ids);
        FaceMatch::whereIn('id', $request->match_ids)
            ->update([
                'status' => 'verified',
                'verified_at' => now(),
            ]);

        // ✅ **NEW: Batch verification notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Batch Verification Complete",
            "You verified {$count} face matches in batch",
            'success',
            route('forensic.face.match')
        );

        return back()->with('success', $count . ' matches verified successfully.');
    }
}