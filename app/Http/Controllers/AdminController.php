<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use App\Models\Transcription;
use App\Models\TranscriptionVerification;
use App\Services\TranscriptionService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $transcriptionService;
    
    public function __construct(TranscriptionService $transcriptionService)
    {
        $this->middleware('role:admin');
        $this->transcriptionService = $transcriptionService;
    }
    
    // Dashboard with analytics
    public function dashboard()
    {
        $analytics = $this->transcriptionService->getAdminAnalytics();
        
        return view('admin.dashboard', compact('analytics'));
    }
    
    // View all transcriptions
    public function transcriptions(Request $request)
    {
        $query = Transcription::with(['user', 'complaint', 'verifications']);
        
        // Search filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('original_text', 'like', '%' . $request->search . '%')
                  ->orWhere('roman_text', 'like', '%' . $request->search . '%')
                  ->orWhereHas('complaint', function($q2) use ($request) {
                      $q2->where('id', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('user_type')) {
            if ($request->user_type === 'public') {
                $query->whereNull('user_id');
            } else {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('role', $request->user_type);
                });
            }
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transcriptions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.transcriptions.index', compact('transcriptions'));
    }
    
    // View specific transcription details
    public function transcriptionDetail($id)
    {
        $transcription = Transcription::with([
            'user', 
            'complaint', 
            'verifications.analyst',
            'media'
        ])->findOrFail($id);
        
        return view('admin.transcriptions.detail', compact('transcription'));
    }
    
    // AI usage statistics
    public function aiStatistics()
    {
        $stats = [
            'total_transcriptions' => Transcription::count(),
            'success_rate' => Transcription::where('status', 'completed')
                ->orWhere('status', 'verified')
                ->count() / max(Transcription::count(), 1) * 100,
            'avg_processing_time' => $this->calculateAvgProcessingTime(),
            'module_usage' => [
                'public' => Transcription::whereNull('user_id')->count(),
                'police' => Transcription::whereHas('user', function($q) {
                    $q->where('role', 'police');
                })->count(),
                'forensic' => TranscriptionVerification::count()
            ],
            'daily_volume' => $this->getDailyVolume(30),
            'accuracy_rate' => TranscriptionVerification::where('approved', true)->count() / 
                               max(TranscriptionVerification::count(), 1) * 100
        ];
        
        return view('admin.ai-statistics', compact('stats'));
    }
    
    // Export data
    public function exportTranscriptions(Request $request)
    {
        $transcriptions = Transcription::with(['user', 'complaint'])
            ->whereDate('created_at', '>=', $request->date_from ?? now()->subMonth())
            ->whereDate('created_at', '<=', $request->date_to ?? now())
            ->get();
        
        $pdf = app('dompdf.wrapper')->loadView('admin.exports.transcriptions', compact('transcriptions'));
        
        return $pdf->download('transcriptions-' . date('Y-m-d') . '.pdf');
    }
    
    // Manage system settings
    public function aiSettings()
    {
        $settings = [
            'whisper_model' => config('whisper.model', 'base'),
            'default_language' => config('whisper.language', 'ur'),
            'max_audio_size' => config('audio.max_size', 10240),
            'auto_assign_forensic' => config('system.auto_assign', true),
            'require_verification' => config('system.require_verification', true)
        ];
        
        return view('admin.settings.ai', compact('settings'));
    }
    
    private function calculateAvgProcessingTime()
    {
        // Calculate average transcription processing time
        $times = Transcription::where('status', 'completed')
            ->get()
            ->map(function($t) {
                return $t->created_at->diffInSeconds($t->updated_at);
            });
        
        return $times->avg() ?? 0;
    }
    
    private function getDailyVolume($days = 30)
    {
        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Transcription::whereDate('created_at', $date)->count();
            $data[$date] = $count;
        }
        
        return $data;
    }
}