<?php
// app/Http/Controllers/TranscriptionController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Transcription;
use App\Services\TranscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TranscriptionController extends Controller
{
   public function showForm()
{
    $mediaFiles = Media::whereIn('file_type', ['audio', 'video'])->get();

    return view('form', compact('mediaFiles'));
}

    public function transcribe(Request $request)
    {
        $request->validate([
            'media_id' => 'required|exists:media_uploads,id',
        ]);

        $media = Media::findOrFail($request->media_id);

        // ❌ Only audio/video allowed
        if (!in_array($media->file_type, ['audio', 'video'])) {
            return back()->with('error', 'Only audio/video files can be transcribed.');
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 1: Basic transcription (dummy / placeholder)
        | (Real world: Whisper / Google STT)
        |--------------------------------------------------------------------------
        */
        $rawTranscript = "This is an auto-generated transcript from audio/video file.";

        /*
        |--------------------------------------------------------------------------
        | STEP 2: Enhance transcript using Gemini
        |--------------------------------------------------------------------------
        */
        $geminiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key='
            . config('services.gemini.key'),
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' =>
                                "Improve and clean this police evidence transcript:\n\n" . $rawTranscript
                            ]
                        ]
                    ]
                ]
            ]
        );

        $finalTranscript = $geminiResponse
            ->json('candidates.0.content.parts.0.text')
            ?? $rawTranscript;

        /*
        |--------------------------------------------------------------------------
        | STEP 3: Save transcription per MEDIA (NOT complaint)
        |--------------------------------------------------------------------------
        */
        Transcription::updateOrCreate(
            ['media_id' => $media->id],
            [
                'complaint_id' => $media->complaint_id,
                'transcript' => $finalTranscript
            ]
        );

        return back()->with('success', 'Transcription generated successfully.');
    }
}

// class TranscriptionController extends Controller
// {
//     protected $transcriptionService;
    
//     public function __construct(TranscriptionService $transcriptionService)
//     {
//         $this->transcriptionService = $transcriptionService;
//     }
//       public function showForm()
//     {
//         return view('form');
//     }
//    /**
//      * Handle form submission - FIXED FOR WINDOWS
//      */
//    // app/Http\Controllers/TranscriptionController.php

// public function handleUpload(Request $request)
// {
//     // Force JSON response
//     header('Content-Type: application/json');
    
//     try {
//         Log::info('=== UPLOAD REQUEST START ===');
        
//         $request->validate([
//             'audio' => 'required|file|mimes:mp3,wav,m4a,ogg,flac|max:10240',
//             'language' => 'required|in:ur,en,hi',
//         ]);
        
//         $audioFile = $request->file('audio');
//         $originalName = $audioFile->getClientOriginalName();
        
//         Log::info('File received', [
//             'name' => $originalName,
//             'size' => $audioFile->getSize(),
//             'type' => $audioFile->getMimeType()
//         ]);
        
//         // Save to temporary location
//         $tempDir = sys_get_temp_dir() . '/laravel_audio';
//         if (!file_exists($tempDir)) {
//             mkdir($tempDir, 0755, true);
//         }
        
//         $filename = 'upload_' . time() . '_' . preg_replace('/[^A-Za-z0-9\.]/', '_', $originalName);
//         $audioPath = $tempDir . '/' . $filename;
        
//         // Move uploaded file
//         $audioFile->move($tempDir, $filename);
        
//         if (!file_exists($audioPath)) {
//             throw new \Exception('Failed to save uploaded file');
//         }
        
//         Log::info('File saved', [
//             'temp_path' => $audioPath,
//             'size' => filesize($audioPath)
//         ]);
        
//         // Perform transcription
//         $language = $request->input('language', 'ur');
//         $service = app(\App\Services\TranscriptionService::class);
//         $result = $service->transcribeAudio($audioPath, $language);
        
//         // Clean up
//         if (file_exists($audioPath)) {
//             unlink($audioPath);
//             Log::info('Temp file cleaned up');
//         }
        
//         Log::info('=== UPLOAD REQUEST COMPLETE ===');
        
//         return response()->json($result);
        
//     } catch (\Illuminate\Validation\ValidationException $e) {
//         Log::error('Validation error', ['errors' => $e->errors()]);
        
//         return response()->json([
//             'success' => false,
//             'error' => 'Validation failed',
//             'urdu_text' => 'غلطی: درخواست کی جانچ ناکام۔',
//             'roman_text' => 'Error: Request validation failed.',
//             'confidence' => 0.0
//         ], 422);
        
//     } catch (\Exception $e) {
//         Log::error('Upload error: ' . $e->getMessage());
        
//         return response()->json([
//             'success' => false,
//             'error' => $e->getMessage(),
//             'urdu_text' => 'خرابی: ' . $e->getMessage(),
//             'roman_text' => 'Error: ' . $e->getMessage(),
//             'confidence' => 0.0
//         ], 500);
//     }
// }
    
//     /**
//      * SIMPLE TEST ENDPOINT
//      */
//     public function simpleUpload(Request $request)
//     {
//         Log::info('Simple upload test called');
        
//         try {
//             if (!$request->hasFile('audio')) {
//                 return response()->json([
//                     'success' => false,
//                     'error' => 'No file uploaded',
//                     'received_data' => $request->all()
//                 ]);
//             }
            
//             $file = $request->file('audio');
            
//             // **SOLUTION: Use system temp directory**
//             $tempDir = sys_get_temp_dir();
//             $tempPath = $tempDir . '/test_' . time() . '_' . $file->getClientOriginalName();
            
//             Log::info('Test upload details:', [
//                 'original_name' => $file->getClientOriginalName(),
//                 'temp_path' => $tempPath,
//                 'temp_dir' => $tempDir,
//                 'temp_dir_writable' => is_writable($tempDir)
//             ]);
            
//             // Method 1: Using move_uploaded_file
//             $uploaded = move_uploaded_file(
//                 $file->getPathname(),
//                 $tempPath
//             );
            
//             if (!$uploaded) {
//                 // Method 2: Using copy
//                 $uploaded = copy($file->getPathname(), $tempPath);
//             }
            
//             if (!$uploaded) {
//                 // Method 3: Using Laravel store
//                 $path = $file->storeAs('test_direct', $file->getClientOriginalName());
//                 $tempPath = storage_path('app/' . $path);
//             }
            
//             return response()->json([
//                 'success' => true,
//                 'message' => 'File upload test',
//                 'original_name' => $file->getClientOriginalName(),
//                 'temp_path' => $tempPath,
//                 'file_exists' => file_exists($tempPath) ? 'YES' : 'NO',
//                 'file_size' => file_exists($tempPath) ? filesize($tempPath) : 0,
//                 'is_readable' => file_exists($tempPath) ? (is_readable($tempPath) ? 'YES' : 'NO') : 'N/A',
//                 'upload_method' => 'mixed',
//                 'system_temp_dir' => sys_get_temp_dir(),
//                 'storage_path' => storage_path(),
//                 'base_path' => base_path()
//             ]);
            
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'trace' => $e->getTraceAsString()
//             ], 500);
//         }
//     }
    
//     /**
//      * DIRECT TRANSCRIPTION WITH CUSTOM PATH
//      */
//     public function directTranscribe(Request $request)
//     {
//         try {
//             $request->validate([
//                 'audio' => 'required|file|mimes:mp3,wav,m4a,ogg,flac|max:10240',
//                 'language' => 'required|in:ur,en,hi',
//             ]);
            
//             $file = $request->file('audio');
            
//             // **BEST SOLUTION: Save to project root temp directory**
//             $projectRoot = base_path();
//             $tempDir = $projectRoot . '/temp_audio';
            
//             // Create directory if not exists
//             if (!is_dir($tempDir)) {
//                 mkdir($tempDir, 0755, true);
//             }
            
//             $filename = 'upload_' . time() . '_' . $file->getClientOriginalName();
//             $audioPath = $tempDir . '/' . $filename;
            
//             // Save file using simple PHP methods
//             $tempName = $file->getPathname();
            
//             if (move_uploaded_file($tempName, $audioPath)) {
//                 Log::info('File moved to: ' . $audioPath);
//             } elseif (copy($tempName, $audioPath)) {
//                 Log::info('File copied to: ' . $audioPath);
//             } else {
//                 // Last resort: read and write
//                 $content = file_get_contents($tempName);
//                 file_put_contents($audioPath, $content);
//                 Log::info('File written to: ' . $audioPath);
//             }
            
//             // Verify
//             if (!file_exists($audioPath)) {
//                 throw new \Exception('File not saved: ' . $audioPath);
//             }
            
//             // Perform transcription
//             $language = $request->input('language', 'ur');
//             $result = $this->transcriptionService->transcribeAudio($audioPath, $language);
            
//             // Clean up
//             if (file_exists($audioPath)) {
//                 unlink($audioPath);
//             }
            
//             return response()->json($result);
            
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'urdu_text' => 'ٹرانککرپشن ناکام: ' . $e->getMessage(),
//                 'roman_text' => 'Transcription failed: ' . $e->getMessage(),
//                 'confidence' => 0.0
//             ], 500);
//         }
//     }

    
// }