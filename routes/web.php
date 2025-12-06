<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PublicAlertController;
use App\Http\Controllers\Admin\SummaryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ComplaintTrackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Forensic\ForensicController;
use App\Http\Controllers\ForensicController\AIController;
use App\Http\Controllers\ForensicController\AssignedCasesController;

use App\Http\Controllers\ForensicController\AudioVideoSegmentationController;
use App\Http\Controllers\ForensicController\CaseDetailsController;
use App\Http\Controllers\ForensicController\DashboardController as ForensicControllerDashboardController;
use App\Http\Controllers\ForensicController\FaceMatchingController;
use App\Http\Controllers\ForensicController\FinalizeReportController;
use App\Http\Controllers\ForensicController\ReportController;
use App\Http\Controllers\ForensicController\SummaryApprovalController;
use App\Http\Controllers\ForensicController\TranscriptVerificationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Police\AddFIRController;
use App\Http\Controllers\Police\EvidenceController;
use App\Http\Controllers\Police\ForwardCaseController;
use App\Http\Controllers\Police\PoliceController;
use App\Http\Controllers\Police\PoliceDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicUser\PublicUserController;
use App\Http\Controllers\UserPublicAlerts;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;














Route::get('/', function () {
    return view('welcome');
});




Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



Route::resource('users', UserController::class);

Route::get('manage-users', [UserController::class, 'index'])->name('manage.users');
// AJAX user search for admin
// Route::get('/admin/user-search', [UserController::class, 'ajaxUserSearch'])->name('admin.user.search');


    // Media Management
    Route::get('/media', [MediaController::class, 'index'])->name('manage.media');
    Route::get('/media/search', [MediaController::class, 'search'])->name('media.search');
    Route::patch('/media/{id}/status', [MediaController::class, 'updateStatus'])->name('media.updateStatus');
Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');




Route::get('ai-feedback', function () {
    return view('admin.ai-feedback');
})->name('ai.feedback');
// Route::get('public-alerts', function () {
//     return view('admin.manage-public-alerts');
// })->name('manage-public.alerts');


// Admin - Manage Public Alerts
Route::get('/admin/public-alerts', [App\Http\Controllers\Admin\PublicAlertController::class, 'index'])->name('admin.public.alerts');
Route::post('/admin/public-alerts/store', [App\Http\Controllers\Admin\PublicAlertController::class, 'store'])->name('admin.public.alerts.store');
Route::delete('/admin/public-alerts/{id}', [App\Http\Controllers\Admin\PublicAlertController::class, 'destroy'])->name('admin.public.alerts.delete');
Route::get('/admin/public-alerts/{id}/edit', [App\Http\Controllers\Admin\PublicAlertController::class, 'edit'])->name('admin.public.alerts.edit');
Route::put('/admin/public-alerts/{id}', [App\Http\Controllers\Admin\PublicAlertController::class, 'update'])->name('admin.public.alerts.update');

Route::get('ai-usage', function () {
    return view('admin.ai-usage');
})->name('ai.usage');
Route::get('analytics', function () {
    return view('admin.analytics');
})->name('analytics');



// Users
Route::get('/admin/user-search', [UserController::class, 'ajaxUserSearch'])->name('admin.user.search');


Route::get('/admin/user-list', [UserController::class, 'ajaxUserList'])->name('admin.user.list');

// Media
Route::get('/admin/media-search', [UserController::class, 'ajaxMediaSearch'])->name('admin.media.search');

// AI
Route::get('/admin/ai-search', [UserController::class, 'ajaxAISearch'])->name('admin.ai.search');

// Analytics
Route::get('/admin/analytics-search', [UserController::class, 'ajaxAnalyticsSearch'])->name('admin.analytics.search');

//complaints
Route::get('/admin/complaints/ajax-search', [AdminComplaintController::class, 'ajaxSearch'])->name('admin.complaints.ajaxSearch');



Route::prefix('admin')->name('admin.')->group(function () {

    // List all complaints with filters
    Route::get('complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');

    // View a single complaint
    Route::get('complaints/{id}', [AdminComplaintController::class, 'show'])->name('complaints.show');

    // Assign complaint to an officer
    Route::post('complaints/{id}/assign', [AdminComplaintController::class, 'assign'])->name('complaints.assign');

    // Change complaint status
    Route::post('complaints/{id}/change-status', [AdminComplaintController::class, 'changeStatus'])->name('complaints.changeStatus');

    // Delete complaint
    Route::delete('/complaints/{id}', [AdminComplaintController::class, 'destroy'])->name('complaints.destroy');
    // ✅ Update complaint (status, officer, notes)
    Route::put('complaints/{id}', [AdminComplaintController::class, 'update'])->name('complaints.update');
});




    Route::get('/summaries', [SummaryController::class, 'index'])->name('admin.summaries');
    Route::post('/summaries/{id}/approve', [SummaryController::class, 'approve'])->name('admin.summaries.approve');
    Route::post('/summaries/{id}/reject', [SummaryController::class, 'reject'])->name('admin.summaries.reject');

    

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/verify', [AuthController::class, 'showVerifyForm'])->name('verify.email');
Route::post('/verify', [AuthController::class, 'verifyOtp'])->name('verify.email.submit');
Route::post('/resend-otp', [AuthController::class, 'resend'])->name('resend.otp');


Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    
});
 
  Route::post('change-role/{user}', [AuthController::class, 'updateRole'])->name('users.updateRole');







  //Public User Routes
  Route::get('/Public/welcome',[ UserPublicAlerts::class,'welcomeIndex'])->name('public.welcome');
    Route::get('/Public/dashboard',[UserPublicAlerts::class, 'index'])
    ->name('public.dashboard');
//     Route::get('/Public/complaints-form', function () {
//     return view('public_user.complaints-form');
// })->name('public.complaints.form');
Route::get('/Public/complaints-form', [ComplaintController::class, 'store'])->name('public.complaints.form');
  Route::get('/Public/complaints-form', [ComplaintController::class, 'create'])->name('public.complaints.form');
  Route::get('/Public/complaints-track', [ComplaintTrackController::class, 'index'])->name('public.complaints.track');
    Route::post('/preview-voice', [ComplaintController::class, 'previewVoiceComplaint'])->name('complaints.preview.voice');
    Route::get('/{id}/transcription', [ComplaintController::class, 'getTranscription']);
    Route::get('/{id}/transcription-status', [ComplaintController::class, 'checkTranscriptionStatus']);
    Route::delete('/hide/{id}', [ComplaintController::class, 'hide'])->name('complaints.hide');

    
   Route::post('/api/transcribe', [ComplaintController::class, 'previewVoiceComplaint']);
    Route::get('/api/complaint/{id}/transcription', [ComplaintController::class, 'getTranscription']);




    Route::get('/Public/public-alerts', [UserPublicAlerts::class, 'allAlerts'])
     ->name('public.alerts');
    Route::get('/Public/profile-update', function () {
        return view('public_user.profile-update');
    })->name('public.profile.update');



    Route::post('/complaints/store', [ComplaintController::class, 'store'])->name('complaints.store');


Route::get('/complaints-track', [ComplaintTrackController::class, 'index'])->name('complaints.track');
Route::post('/complaints-track', [ComplaintTrackController::class, 'track'])->name('complaints.track.submit');
Route::delete('/complaints/{id}/hide', [ComplaintController::class, 'hide'])->name('complaints.hide');



// routes/web.php


//police officer routes

// Route::get('police/add-fir',function(){
//     return view('police.add-fir');
// })->name('police.add-fir');
// Route::get('police/cases',function(){
//     return view('police.cases');
// })->name('police.cases');
Route::get('police/ai-tools',function(){
    return view('police.ai-tools');
})->name('police.ai-tools');
// Route::get('police/manage-evidence',function(){
//     return view('police.manage-evidence');
// })->name('police.upload-evidence');
// Route::get('police/forward-case',function(){
//     return view('police.forward-case');
// })->name('police.forward-case');




    Route::get('/police/dashboard', [PoliceDashboardController::class, 'index'])->name('police.dashboard');

Route::put('/police/cases/{id}', [PoliceDashboardController::class, 'update'])->name('police.cases.update');

Route::get('/add-fir', [AddFIRController::class, 'create'])->name('police.add-fir');
Route::post('/add-fir', [AddFIRController::class, 'store'])->name('police.store_fir');

Route::get('/police/cases', [App\Http\Controllers\Police\ComplaintController::class, 'index'])->name('police.cases');
Route::put('/police/cases/{id}/update', [App\Http\Controllers\Police\ComplaintController::class, 'update'])->name('police.cases.update');


    Route::get('/evidence', [EvidenceController::class, 'index'])->name('police.upload-evidence');
    Route::post('/evidence/upload', [EvidenceController::class, 'store'])->name('police.evidence.store');
    Route::delete('/evidence/{id}', [EvidenceController::class, 'destroy'])->name('police.evidence.destroy');



// Forward Case Routes
    Route::get('/forward-case', [ForwardCaseController::class, 'index'])->name('police.forward-case');
    Route::post('/forward-case', [ForwardCaseController::class, 'forward'])->name('police.forward.cases');



    //Forensic Analyst Routes
    Route::get('/forensic/dashboard', [App\Http\Controllers\ForensicController\DashboardController::class, 'dashboard'])->name('forensic.dashboard');
    Route::get('/forensic/case/{trackId}', [App\Http\Controllers\ForensicController\DashboardController::class, 'showCaseDetail'])->name('forensic.case.detail');
Route::get('/preview/{path}', function ($path) {

    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404, 'File not found: ' . $fullPath);
    }

    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

    $mime = [
        'jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','bmp'=>'image/bmp','webp'=>'image/webp',
        'mp4'=>'video/mp4','webm'=>'video/webm','mkv'=>'video/x-matroska','mov'=>'video/quicktime',
        'mp3'=>'audio/mpeg','wav'=>'audio/wav','ogg'=>'audio/ogg','aac'=>'audio/aac',
        'pdf'=>'application/pdf',
        'doc'=>'application/msword','docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'pptx'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt'=>'text/plain'
    ][$ext] ?? 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
    ]);

})->where('path', '.*')->name('file.preview');


    Route::get('/forensic/assigned-cases', [App\Http\Controllers\ForensicController\AssignedCasesController::class, 'assignedCases'])->name('forensic.assigned-cases');
    Route::get('/forensic/case-details/{id}', [App\Http\Controllers\ForensicController\CaseDetailsController::class, 'caseDetails'])->name('forensic.case.details');
    // Route::post('/forensic/case/{id}/review', [ForensicController::class, 'submitReview'])->name('forensic.submitReview');
    // AI-assisted actions
// Route::post('/forensic/case/{id}/ai/transcribe', [ForensicController::class, 'aiTranscribe'])->name('forensic.ai.transcribe');
// Route::post('/forensic/case/{id}/ai/summarize', [ForensicController::class, 'aiSummarize'])->name('forensic.ai.summarize');
// Route::post('/forensic/case/{id}/ai/face-detect', [ForensicController::class, 'aiFaceDetect'])->name('forensic.ai.faceDetect');
// Route::post('/forensic/case/{id}/ai/report', [ForensicController::class, 'aiReport'])->name('forensic.ai.report');



 Route::post('/case/{id}/summarize', [AIController::class, 'generateSummary'])
        ->name('forensic.ai.summarize');

    Route::get('/case/{id}/report', [AIController::class, 'generateAIReport'])
        ->name('forensic.report');

        

    Route::post('/case/{id}/review', [CaseDetailsController::class, 'submitReview'])
        ->name('forensic.submitReview');

Route::get('/forensic/case/{id}/report', [AssignedCasesController::class, 'showReport'])->name('forensic.report');
Route::post('/forensic/case/{id}/report/export', [AssignedCasesController::class, 'exportReport'])->name('forensic.report.export');



    // Route::get('/face-matching', [FaceMatchingController::class, 'index'])->name('forensic.face.match');
    // Route::post('/face-matching', [FaceMatchingController::class, 'match'])->name('forensic.face.match.post');

  // Face Matching Routes
    Route::get('/face-matching', [FaceMatchingController::class, 'index'])
        ->name('forensic.face.match');
        
    Route::post('/face-matching/match', [FaceMatchingController::class, 'match'])
        ->name('forensic.face.match.post');
        
    Route::post('/face-matching/verify/{id}', [FaceMatchingController::class, 'verifyMatch'])
        ->name('forensic.face.verify');
        
    Route::post('/face-matching/reject/{id}', [FaceMatchingController::class, 'rejectMatch'])
        ->name('forensic.face.reject');
        
    Route::delete('/face-matching/delete/{id}', [FaceMatchingController::class, 'deleteMatch'])
        ->name('forensic.face.delete');
        
    Route::get('/face-matching/view/{id}', [FaceMatchingController::class, 'viewMatch'])
        ->name('forensic.face.view');
        
    Route::get('/face-matching/statistics', [FaceMatchingController::class, 'getStatistics'])
        ->name('forensic.face.statistics');
        
    Route::get('/face-matching/case/{id}/matches', [FaceMatchingController::class, 'getCaseMatches'])
        ->name('forensic.face.case-matches');
        
    Route::post('/face-matching/batch-verify', [FaceMatchingController::class, 'batchVerify'])
        ->name('forensic.face.batch-verify');





    Route::get('/audio-video-segmentation', [AudioVideoSegmentationController::class, 'index'])->name('forensic.audio-video');
    Route::post('/audio-video-segmentation', [AudioVideoSegmentationController::class, 'segment'])->name('forensic.audio-video.segment');


        Route::get('/transcript-verification', [TranscriptVerificationController::class, 'index'])->name('forensic.transcript');
    Route::post('/transcript-verification', [TranscriptVerificationController::class, 'update'])->name('forensic.transcript.update');
  Route::get('/forensic/transcript-verification/status/{mediaId}', [TranscriptVerificationController::class, 'getVerificationStatus'])
        ->name('forensic.transcript.status');
      
      
    Route::get('/summary-approval', [SummaryApprovalController::class, 'index'])->name('forensic.summary');
Route::post('/summary-approval', [SummaryApprovalController::class, 'update'])->name('forensic.summary.update');

Route::get('/summary-approval/approved', [SummaryApprovalController::class, 'approved'])
    ->name('forensic.summary.approved');
Route::get('/summary-approval/{complaintId}', [SummaryApprovalController::class, 'show'])
    ->name('forensic.summary.detail');


        Route::get('/finalize-report', [FinalizeReportController::class, 'index'])->name('forensic.finalize');
    Route::get('/finalize-report/export/{id}', [FinalizeReportController::class, 'exportPDF'])->name('forensic.finalize.export');
    Route::get('/finalize-report', [FinalizeReportController::class, 'index'])
        ->name('forensic.finalize');
        
    Route::get('/finalize-report/export/{id}', [FinalizeReportController::class, 'exportPDF'])
        ->name('forensic.finalize.export');
        
    Route::get('/finalize-report/generated', [FinalizeReportController::class, 'generatedReports'])
        ->name('forensic.finalize.generated');
        
    Route::get('/finalize-report/view/{id}', [FinalizeReportController::class, 'viewReport'])
        ->name('forensic.finalize.view');
        
    Route::get('/finalize-report/download/{id}', [FinalizeReportController::class, 'downloadReport'])
        ->name('forensic.finalize.download');

// web.php میں
Route::delete('/finalize-report/delete/{id}', [FinalizeReportController::class, 'deleteReport'])
    ->name('forensic.finalize.delete');
Route::get('/forensic/cases/{id}/export-pdf', [AIController::class, 'exportPDF'])
    ->name('forensic.export.pdf');
    // web.php میں
Route::get('/finalize-report/check-file/{id}', [FinalizeReportController::class, 'checkFile'])
    ->name('forensic.finalize.check-file');







    // Notifications Routes
Route::middleware(['auth'])->group(function () {
    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/clear-all', [NotificationController::class, 'clearAll']);
    });
});




// Public Routes (No authentication required)
Route::prefix('public')->group(function () {
    Route::post('/complaint/voice', [PublicUserController::class, 'submitVoiceComplaint']);
    Route::post('/complaint/preview', [PublicUserController::class, 'previewTranscription']);
    Route::get('/complaint/status/{id}', [PublicUserController::class, 'checkStatus']);
});

// Police Routes
Route::prefix('police')->middleware(['auth', 'role:police'])->group(function () {
    Route::post('/fir/record', [PoliceController::class, 'recordFIR']);
    // Route::get('/cases', [PoliceController::class, 'myCases']);
    Route::put('/transcription/{id}/edit', [PoliceController::class, 'editTranscription']);
});

// Forensic Routes
Route::prefix('forensic')->middleware(['auth', 'role:forensic'])->group(function () {
    Route::get('/verifications', [ForensicController::class, 'pendingVerifications']);
    Route::post('/verify/{id}', [ForensicController::class, 'verify']);
    Route::get('/verified', [ForensicController::class, 'verifiedTranscriptions']);
    Route::post('/report/generate/{id}', [ForensicController::class, 'generateReport']);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/transcriptions', [AdminController::class, 'transcriptions']);
    Route::get('/transcription/{id}', [AdminController::class, 'transcriptionDetail']);
    Route::get('/ai-statistics', [AdminController::class, 'aiStatistics']);
    Route::post('/export/transcriptions', [AdminController::class, 'exportTranscriptions']);
    Route::get('/settings/ai', [AdminController::class, 'aiSettings']);
});



// routes/web.php میں

use App\Http\Controllers\TranscriptionController;

// routes/web.php میں
// routes/web.php میں
Route::get('/test-offline', function() {
    $testAudio = 'C:\Users\PMLS\Downloads\ghazals_ghalib_0809_librivox\aahkochaahyea_ghalib_64kb.mp3';
    
    $process = new \Symfony\Component\Process\Process([
        'python',
        base_path('app/PythonScripts/transcribe.py'),
        $testAudio,
        'ur'
    ]);
    
    $process->setTimeout(300);
    $process->run();
    
    return response()->json([
        'exit_code' => $process->getExitCode(),
        'stdout' => $process->getOutput(),
        'stderr' => $process->getErrorOutput(),
        'stdout_decoded' => $process->getOutput()
    ]);
});
Route::get('/test-python-direct', function() {
    // Test the exact command that works in CMD
    $testAudio = 'C:\Users\PMLS\Downloads\ghazals_ghalib_0809_librivox\aahkochaahyea_ghalib_64kb.mp3';
    
    if (!file_exists($testAudio)) {
        return response()->json([
            'error' => 'Test audio not found',
            'path' => $testAudio
        ]);
    }
    
    // Method 1: Direct command (like CMD)
    $command = 'python "' . base_path('app/PythonScripts/transcribe.py') . '" "' . $testAudio . '" ur';
    
    // Method 2: Using Process component
    $process = new \Symfony\Component\Process\Process([
        'python',
        base_path('app/PythonScripts/transcribe.py'),
        $testAudio,
        'ur'
    ]);
    
    $process->setTimeout(300);
    $process->run();
    
    return response()->json([
        'command' => $command,
        'exit_code' => $process->getExitCode(),
        'stdout' => $process->getOutput(),
        'stderr' => $process->getErrorOutput(),
        'stdout_decoded' => json_decode('"' . $process->getOutput() . '"'),
        'file_info' => [
            'path' => $testAudio,
            'exists' => file_exists($testAudio),
            'size' => filesize($testAudio)
        ]
    ]);
});
// routes/web.php میں test route بنائیں
Route::get('/test-python-script', function() {
    $pythonPath = 'python'; // یا 'D:\Python\python.exe'
    $scriptPath = base_path('app/PythonScripts/transcribe.py');
    
    // Test audio file - آپ نے جو CMD میں استعمال کیا تھا
    $testAudio = 'C:\Users\PMLS\Downloads\ghazals_ghalib_0809_librivox\aahkochaahyea_ghalib_64kb.mp3';
    
    // یا کوئی اور test file
    // $testAudio = storage_path('app/test_audio.mp3');
    
    if (!file_exists($testAudio)) {
        // ایک simple test audio بنائیں
        $testAudio = storage_path('app/test_simple.wav');
        
        // Create a simple WAV file
        $wavContent = base64_decode('UklGRjIAAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQ4AAA==');
        file_put_contents($testAudio, $wavContent);
    }
    
    $command = [
        $pythonPath,
        $scriptPath,
        $testAudio,
        'ur'
    ];
    
    $process = new \Symfony\Component\Process\Process($command);
    $process->setTimeout(120);
    $process->run();
    
    return response()->json([
        'python_path' => $pythonPath,
        'script_path' => $scriptPath,
        'audio_path' => $testAudio,
        'audio_exists' => file_exists($testAudio),
        'exit_code' => $process->getExitCode(),
        'is_successful' => $process->isSuccessful(),
        'stdout' => $process->getOutput(),
        'stderr' => $process->getErrorOutput()
    ]);
});
// Transcription Form Routes
Route::get('/transcribe', [TranscriptionController::class, 'showForm'])->name('transcribe.form');
Route::get('/transcribe/form', [TranscriptionController::class, 'showForm']);
// routes/web.php میں

// Simple test routes
Route::post('/transcribe/simple-test', [TranscriptionController::class, 'simpleUpload']);
Route::post('/transcribe/direct', [TranscriptionController::class, 'directTranscribe']);

// Main route (temporary fix - use direct route)
Route::post('/transcribe/upload', [TranscriptionController::class, 'directTranscribe'])
    ->name('transcribe.upload')
    ->withoutMiddleware(['csrf']);
Route::get('/transcribe/direct', [TranscriptionController::class, 'directTranscription'])->name('transcribe.direct');
Route::get('/transcribe/history', [TranscriptionController::class, 'getHistory'])->name('transcribe.history');




Route::middleware('auth')->group(function() {
    Route::get('transcription/form/{complaintId}', [TranscriptionController::class, 'showForm'])->name('transcription.form');
    Route::post('transcription/upload', [TranscriptionController::class, 'uploadAudio'])->name('transcription.upload');
    Route::get('transcription/preview/{id}', [TranscriptionController::class, 'preview'])->name('transcription.preview');
    Route::post('transcription/save/{id}', [TranscriptionController::class, 'saveCorrection'])->name('transcription.save');
});

// routes/web.php میں
Route::get('/test-python-fix', function() {
    $pythonPath = 'D:\Python\python.exe';
    $testScript = storage_path('app/test_fix.py');
    
    // Simple test script بنائیں
    $scriptContent = <<<'PYTHON'
import sys
print("Python version:", sys.version)
print("Platform:", sys.platform)
try:
    import asyncio
    print("Asyncio imported successfully")
    if sys.platform == 'win32':
        asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())
        print("Event loop policy set")
except Exception as e:
    print(f"Asyncio error: {e}")
    
print("Script completed successfully")
PYTHON;
    
    file_put_contents($testScript, $scriptContent);
    
    $process = new \Symfony\Component\Process\Process([
        $pythonPath,
        $testScript
    ]);
    
    $process->setTimeout(10);
    $process->run();
    
    return response()->json([
        'exit_code' => $process->getExitCode(),
        'output' => $process->getOutput(),
        'error' => $process->getErrorOutput()
    ]);
});

// routes/web.php میں شامل کریں
Route::get('/test-transcription-debug', function() {
    $pythonPath = 'D:\Python\python.exe';
    $pythonScript = 'D:\web development\laravel\FYP\app\PythonScripts\transcribe.py';
    
    // ایک test audio file بنائیں یا موجودہ استعمال کریں
    $testAudio = 'C:\Users\PMLS\Downloads\ghazals_ghalib_0809_librivox\aashiq_ghalib_64kb.mp3';
    
    // اگر test file نہیں ہے تو بنائیں
    if (!file_exists($testAudio)) {
        // ایک empty WAV file بنائیں
        $wavHeader = pack('cccccccccc', 82, 73, 70, 70, 40, 0, 0, 0, 87, 65);
        file_put_contents($testAudio, $wavHeader);
    }
    
    $process = new \Symfony\Component\Process\Process([
        $pythonPath,
        $pythonScript,
        $testAudio,
        'ur'
    ]);
    
    $process->setTimeout(30);
    $process->run();
    
    return response()->json([
        'python_path' => $pythonPath,
        'script_path' => $pythonScript,
        'audio_path' => $testAudio,
        'exit_code' => $process->getExitCode(),
        'is_successful' => $process->isSuccessful(),
        'stdout' => $process->getOutput(),
        'stderr' => $process->getErrorOutput(),
        'stdout_length' => strlen($process->getOutput()),
        'stderr_length' => strlen($process->getErrorOutput())
    ]);
});


// routes/web.php میں
Route::get('/test-cmd-transcription', function() {
    // Test audio file
    $testAudio = storage_path('app/public/test.mp3');
    
    // اگر test file موجود نہیں ہے تو بنائیں
    if (!file_exists($testAudio)) {
        // ایک sample MP3 file بنائیں یا ڈاؤن لوڈ کریں
        // آپ کوئی بھی short MP3 file استعمال کر سکتے ہیں
        $sampleContent = base64_decode('// MP3 کا sample content');
        file_put_contents($testAudio, $sampleContent);
    }
    
    // وہ command جو CMD میں کام کرتی ہے
    $command = [
        'python',
        '-c',
        '"import whisper; model = whisper.load_model(\'tiny\'); result = model.transcribe(\'' . $testAudio . '\', language=\'ur\'); print(result[\'text\'])"'
    ];
    
    // یا اگر آپ کے پاس separate script ہے
    // $command = ['python', 'D:\path\to\transcribe.py', $testAudio, 'ur'];
    
    $process = new \Symfony\Component\Process\Process($command);
    $process->setTimeout(300); // 5 minutes
    $process->run();
    
    return response()->json([
        'command' => implode(' ', $command),
        'exit_code' => $process->getExitCode(),
        'is_successful' => $process->isSuccessful(),
        'output' => $process->getOutput(),
        'error' => $process->getErrorOutput()
    ]);
});




// Transcription routes
Route::get('/transcribe/test', [TranscriptionController::class, 'testTranscription']);
Route::post('/transcribe/upload', [TranscriptionController::class, 'transcribeUpload']);
Route::post('/transcribe/by-path', [TranscriptionController::class, 'transcribeByPath']);
Route::get('/transcribe/health', [TranscriptionController::class, 'systemHealth']);

// Simple direct test - بالکل CMD والی command
Route::get('/transcribe/direct-test', function() {
    $pythonPath = 'python';
    $scriptPath = base_path('app/PythonScripts/transcribe.py');
    $audioPath = 'C:\Users\PMLS\Downloads\ghazals_ghalib_0809_librivox\aahkochaahyea_ghalib_64kb.mp3';
    
    // Check if files exist
    if (!file_exists($scriptPath)) {
        return response()->json([
            'error' => 'Script not found',
            'script_path' => $scriptPath,
            'exists' => file_exists($scriptPath)
        ]);
    }
    
    if (!file_exists($audioPath)) {
        return response()->json([
            'error' => 'Audio not found',
            'audio_path' => $audioPath,
            'exists' => file_exists($audioPath)
        ]);
    }
    
    $command = [
        $pythonPath,
        $scriptPath,
        $audioPath,
        'ur'
    ];
    
    $process = new \Symfony\Component\Process\Process($command);
    $process->setTimeout(300);
    
    try {
        $process->mustRun();
        
        return response()->json([
            'success' => true,
            'command' => implode(' ', $command),
            'exit_code' => $process->getExitCode(),
            'output' => $process->getOutput(),
            'error_output' => $process->getErrorOutput(),
            'parsed_output' => $this->parseOutput($process->getOutput())
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'command' => implode(' ', $command),
            'exit_code' => $process->getExitCode(),
            'output' => $process->getOutput(),
            'error_output' => $process->getErrorOutput()
        ]);
    }
});

// Helper function to parse output
if (!function_exists('parseOutput')) {
    function parseOutput($output) {
        if (strpos($output, '||') !== false) {
            $parts = explode('||', $output);
            return [
                'urdu_text' => $parts[0] ?? '',
                'roman_text' => $parts[1] ?? '',
                'confidence' => floatval($parts[2] ?? 0.5)
            ];
        }
        return ['raw_output' => $output];
    }
}


// routes/web.php میں

// Transcription Form Routes
Route::get('/transcribe', [TranscriptionController::class, 'showForm'])->name('transcribe.form');
Route::get('/transcribe/form', [TranscriptionController::class, 'showForm']);
Route::post('/transcribe/upload', [TranscriptionController::class, 'handleUpload'])->name('transcribe.upload');
Route::get('/transcribe/direct', [TranscriptionController::class, 'directTranscription'])->name('transcribe.direct');
Route::get('/transcribe/history', [TranscriptionController::class, 'getHistory'])->name('transcribe.history');
// routes/web.php میں

// Test routes
Route::post('/transcribe/test-upload', [TranscriptionController::class, 'testUpload']);
Route::get('/transcribe/status', [TranscriptionController::class, 'checkStatus']);

// Main transcription route with CSRF exemption for testing
Route::post('/transcribe/upload', [TranscriptionController::class, 'handleUpload'])
    ->name('transcribe.upload')
    ->withoutMiddleware(['csrf']); // Remove this after testing
