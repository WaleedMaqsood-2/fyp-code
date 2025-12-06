<?php
// app/Http/Controllers/TranscriptionController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TranscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TranscriptionController extends Controller
{
    protected $transcriptionService;
    
    public function __construct(TranscriptionService $transcriptionService)
    {
        $this->transcriptionService = $transcriptionService;
    }
      public function showForm()
    {
        return view('form');
    }
   /**
     * Handle form submission - FIXED FOR WINDOWS
     */
    public function handleUpload(Request $request)
    {
        // Always return JSON
        header('Content-Type: application/json');
        
        try {
            Log::info('=== TRANSCRIPTION REQUEST START ===');
            
            // Validate request
            $validated = $request->validate([
                'audio' => 'required|file|mimes:mp3,wav,m4a,ogg,flac|max:10240',
                'language' => 'required|in:ur,en,hi',
            ]);
            
            $audioFile = $request->file('audio');
            $originalName = $audioFile->getClientOriginalName();
            
            Log::info('File details:', [
                'original_name' => $originalName,
                'size' => $audioFile->getSize(),
                'mime_type' => $audioFile->getMimeType(),
                'extension' => $audioFile->getClientOriginalExtension()
            ]);
            
            // **SOLUTION 1: Use absolute path in temp directory**
            $tempDir = sys_get_temp_dir() . '/laravel_uploads';
            
            // Create temp directory if not exists
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
                Log::info('Created temp directory: ' . $tempDir);
            }
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9\._\-]/', '_', $originalName);
            $audioPath = $tempDir . '/' . $filename;
            
            Log::info('Attempting to save file:', [
                'temp_dir' => $tempDir,
                'filename' => $filename,
                'audio_path' => $audioPath,
                'temp_dir_exists' => is_dir($tempDir),
                'temp_dir_writable' => is_writable($tempDir)
            ]);
            
            // **SOLUTION 2: Move file using move_uploaded_file (more reliable)**
            $tempUploadPath = $audioFile->getPathname();
            Log::info('Temporary upload path: ' . $tempUploadPath);
            Log::info('Temporary file exists: ' . (file_exists($tempUploadPath) ? 'YES' : 'NO'));
            
            // Copy file to our temp location
            if (copy($tempUploadPath, $audioPath)) {
                Log::info('File copied successfully to: ' . $audioPath);
            } else {
                // Try move_uploaded_file
                if (move_uploaded_file($tempUploadPath, $audioPath)) {
                    Log::info('File moved successfully to: ' . $audioPath);
                } else {
                    // Try Laravel store method as fallback
                    $path = $audioFile->storeAs('temp_audio', $filename, 'local');
                    $audioPath = storage_path('app/' . $path);
                    Log::info('File stored using Laravel: ' . $audioPath);
                }
            }
            
            // Verify file was saved
            if (!file_exists($audioPath)) {
                Log::error('File does not exist after saving attempts: ' . $audioPath);
                
                // Try one more time with simple file_put_contents
                $content = file_get_contents($tempUploadPath);
                if ($content && file_put_contents($audioPath, $content)) {
                    Log::info('File saved using file_put_contents: ' . $audioPath);
                } else {
                    throw new \Exception('Failed to save uploaded file to: ' . $audioPath);
                }
            }
            
            // Verify file is readable and has content
            if (!is_readable($audioPath)) {
                throw new \Exception('File is not readable: ' . $audioPath);
            }
            
            $fileSize = filesize($audioPath);
            Log::info('File verification:', [
                'path' => $audioPath,
                'exists' => file_exists($audioPath) ? 'YES' : 'NO',
                'readable' => is_readable($audioPath) ? 'YES' : 'NO',
                'size' => $fileSize,
                'size_mb' => round($fileSize / (1024 * 1024), 2) . ' MB'
            ]);
            
            if ($fileSize === 0) {
                throw new \Exception('File is empty: ' . $audioPath);
            }
            
            // Get transcription parameters
            $language = $request->input('language', 'ur');
            
            // Perform transcription
            Log::info('Starting transcription process...');
            $result = $this->transcriptionService->transcribeAudio($audioPath, $language);
            Log::info('Transcription completed', [
                'success' => $result['success'] ?? false,
                'text_length' => strlen($result['urdu_text'] ?? '')
            ]);
            
            // Clean up temp file
            if (file_exists($audioPath)) {
                unlink($audioPath);
                Log::info('Cleaned up temp file: ' . $audioPath);
            }
            
            Log::info('=== TRANSCRIPTION REQUEST END ===');
            
            return response()->json($result);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'error' => 'Validation failed: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())),
                'urdu_text' => 'غلطی: درخواست کی جانچ ناکام۔',
                'roman_text' => 'Error: Request validation failed.',
                'confidence' => 0.0
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Transcription error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'urdu_text' => 'ٹرانککرپشن ناکام ہوئی: ' . $e->getMessage(),
                'roman_text' => 'Transcription failed: ' . $e->getMessage(),
                'confidence' => 0.0
            ], 500);
        }
    }
    
    /**
     * SIMPLE TEST ENDPOINT
     */
    public function simpleUpload(Request $request)
    {
        Log::info('Simple upload test called');
        
        try {
            if (!$request->hasFile('audio')) {
                return response()->json([
                    'success' => false,
                    'error' => 'No file uploaded',
                    'received_data' => $request->all()
                ]);
            }
            
            $file = $request->file('audio');
            
            // **SOLUTION: Use system temp directory**
            $tempDir = sys_get_temp_dir();
            $tempPath = $tempDir . '/test_' . time() . '_' . $file->getClientOriginalName();
            
            Log::info('Test upload details:', [
                'original_name' => $file->getClientOriginalName(),
                'temp_path' => $tempPath,
                'temp_dir' => $tempDir,
                'temp_dir_writable' => is_writable($tempDir)
            ]);
            
            // Method 1: Using move_uploaded_file
            $uploaded = move_uploaded_file(
                $file->getPathname(),
                $tempPath
            );
            
            if (!$uploaded) {
                // Method 2: Using copy
                $uploaded = copy($file->getPathname(), $tempPath);
            }
            
            if (!$uploaded) {
                // Method 3: Using Laravel store
                $path = $file->storeAs('test_direct', $file->getClientOriginalName());
                $tempPath = storage_path('app/' . $path);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'File upload test',
                'original_name' => $file->getClientOriginalName(),
                'temp_path' => $tempPath,
                'file_exists' => file_exists($tempPath) ? 'YES' : 'NO',
                'file_size' => file_exists($tempPath) ? filesize($tempPath) : 0,
                'is_readable' => file_exists($tempPath) ? (is_readable($tempPath) ? 'YES' : 'NO') : 'N/A',
                'upload_method' => 'mixed',
                'system_temp_dir' => sys_get_temp_dir(),
                'storage_path' => storage_path(),
                'base_path' => base_path()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * DIRECT TRANSCRIPTION WITH CUSTOM PATH
     */
    public function directTranscribe(Request $request)
    {
        try {
            $request->validate([
                'audio' => 'required|file|mimes:mp3,wav,m4a,ogg,flac|max:10240',
                'language' => 'required|in:ur,en,hi',
            ]);
            
            $file = $request->file('audio');
            
            // **BEST SOLUTION: Save to project root temp directory**
            $projectRoot = base_path();
            $tempDir = $projectRoot . '/temp_audio';
            
            // Create directory if not exists
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $filename = 'upload_' . time() . '_' . $file->getClientOriginalName();
            $audioPath = $tempDir . '/' . $filename;
            
            // Save file using simple PHP methods
            $tempName = $file->getPathname();
            
            if (move_uploaded_file($tempName, $audioPath)) {
                Log::info('File moved to: ' . $audioPath);
            } elseif (copy($tempName, $audioPath)) {
                Log::info('File copied to: ' . $audioPath);
            } else {
                // Last resort: read and write
                $content = file_get_contents($tempName);
                file_put_contents($audioPath, $content);
                Log::info('File written to: ' . $audioPath);
            }
            
            // Verify
            if (!file_exists($audioPath)) {
                throw new \Exception('File not saved: ' . $audioPath);
            }
            
            // Perform transcription
            $language = $request->input('language', 'ur');
            $result = $this->transcriptionService->transcribeAudio($audioPath, $language);
            
            // Clean up
            if (file_exists($audioPath)) {
                unlink($audioPath);
            }
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'urdu_text' => 'ٹرانککرپشن ناکام: ' . $e->getMessage(),
                'roman_text' => 'Transcription failed: ' . $e->getMessage(),
                'confidence' => 0.0
            ], 500);
        }
    }
}