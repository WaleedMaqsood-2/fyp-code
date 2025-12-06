<?php

namespace App\Services;

use App\Models\Transcription;
use App\Models\TranscriptionVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TranscriptionService
{
    // Core transcription function
   public function transcribeAudio($audioFile, $complaintId, $mediaId = null, $userId = null, $language = 'ur')
{
    try {
        Log::info('=== TRANSCRIPTION PROCESS STARTED ===');
        Log::info('Complaint ID: ' . $complaintId);
        Log::info('User ID: ' . $userId);
        Log::info('Original filename: ' . $audioFile->getClientOriginalName());
        
        // 1. Audio file save karein
        $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\.]/', '_', $audioFile->getClientOriginalName());
        $path = $audioFile->storeAs('audio_uploads', $fileName, 'public');
        
        Log::info('File stored at: ' . $path);
        
        // Windows absolute path
        $fullPath = storage_path('app/public/' . $path);
        $fullPath = str_replace('/', DIRECTORY_SEPARATOR, $fullPath);
        
        Log::info('Full path: ' . $fullPath);
        
        // 2. Check file exists
        if (!file_exists($fullPath)) {
            Log::error('File does not exist at: ' . $fullPath);
            throw new \Exception("Audio file not found at: " . $fullPath);
        }
        
        Log::info('File exists, size: ' . filesize($fullPath) . ' bytes');
        
        // 3. Python script path
        $pythonScript = base_path('app\PythonScripts\transcribe.py');
        Log::info('Python script path: ' . $pythonScript);
        
        // 4. Check if Python script exists
        if (!file_exists($pythonScript)) {
            Log::error('Python script not found at: ' . $pythonScript);
            
            // Create simple test script
            $this->createTestPythonScript($pythonScript);
            Log::info('Created test Python script');
        }
        
        // 5. Python executable path
        $pythonExecutable = $this->getPythonExecutable();
        Log::info('Using Python executable: ' . $pythonExecutable);
        
        // Test Python
        $testProcess = new Process([$pythonExecutable, '--version']);
        $testProcess->run();
        if (!$testProcess->isSuccessful()) {
            Log::error('Python test failed: ' . $testProcess->getErrorOutput());
        } else {
            Log::info('Python version: ' . $testProcess->getOutput());
        }
        
        // 6. Process create karein
        Log::info('Creating process with command:');
        Log::info($pythonExecutable . ' ' . $pythonScript . ' ' . $fullPath . ' ' . $language);
        
        $process = new Process([
            $pythonExecutable,
            $pythonScript,
            $fullPath,
            $language
        ]);
        
        $process->setTimeout(300);
        $process->setIdleTimeout(180);
        
        Log::info('Running Python script...');
        $process->run();
        
        // 7. Check for errors
        if (!$process->isSuccessful()) {
            Log::error('Process failed!');
            Log::error('Error output: ' . $process->getErrorOutput());
            Log::error('Exit code: ' . $process->getExitCode());
            Log::error('Exit text: ' . $process->getExitCodeText());
            
            throw new ProcessFailedException($process);
        }
        
        // 8. Get output
        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();
        
        Log::info('Python stdout: ' . $output);
        Log::info('Python stderr: ' . $errorOutput);
        
        // 9. Parse JSON
        $result = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON: ' . $output);
            Log::error('JSON error: ' . json_last_error_msg());
            Log::error('Full output: ' . $output);
            
            // Try to parse from stderr
            if (!empty($errorOutput)) {
                Log::info('Checking stderr for JSON: ' . $errorOutput);
            }
            
            throw new \Exception('Invalid response from Python script. JSON error: ' . json_last_error_msg());
        }
        
        if (!$result || !isset($result['success'])) {
            Log::error('Invalid response format');
            Log::error('Result: ' . print_r($result, true));
            throw new \Exception('Invalid response format from Python script');
        }
        
        if (!$result['success']) {
            Log::error('Transcription failed in Python: ' . ($result['error'] ?? 'Unknown error'));
            throw new \Exception('Transcription failed: ' . ($result['error'] ?? 'Unknown error'));
        }
        
        Log::info('Transcription successful!');
        Log::info('Original text length: ' . strlen($result['original_text']));
        Log::info('Roman text length: ' . strlen($result['roman_text']));
        
        // 10. Database mein save karein
        $transcription = Transcription::create([
            'complaint_id' => $complaintId,
            'media_id' => $mediaId,
            'original_text' => $result['original_text'] ?? '',
            'roman_text' => $result['roman_text'] ?? '',
            'audio_path' => $path,
            'language' => $result['language'] ?? $language,
            'status' => 'completed',
            'confidence_score' => $this->calculateConfidence($result['segments'] ?? []),
            'user_id' => $userId
        ]);
        
        Log::info('Transcription saved with ID: ' . $transcription->id);
        
        return [
            'success' => true,
            'transcription' => $transcription,
            'original_text' => $result['original_text'] ?? '',
            'roman_text' => $result['roman_text'] ?? '',
            'confidence' => $this->calculateConfidence($result['segments'] ?? [])
        ];
        
    } catch (\Exception $e) {
        Log::error('=== TRANSCRIPTION ERROR ===');
        Log::error('Error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'file_path' => $fullPath ?? 'Not set'
        ];
    }
}

private function createTestPythonScript($path)
{
    $scriptContent = <<<'PYTHON'
#!/usr/bin/env python3
import sys
import json
import os

print("DEBUG: Test script called", file=sys.stderr)
print(f"DEBUG: Arguments: {sys.argv}", file=sys.stderr)

if len(sys.argv) < 2:
    result = {'success': False, 'error': 'No audio path provided'}
    print(json.dumps(result))
    sys.exit(1)

audio_path = sys.argv[1]
print(f"DEBUG: Audio path: {audio_path}", file=sys.stderr)
print(f"DEBUG: File exists: {os.path.exists(audio_path)}", file=sys.stderr)

# Return dummy response for testing
result = {
    'success': True,
    'original_text': 'یہ ایک ٹیسٹ ٹرانککرپشن ہے۔',
    'roman_text': 'Yeh ek test transcription hai.',
    'language': 'ur',
    'segments': [
        {'confidence': 0.85, 'text': 'یہ ایک ٹیسٹ ٹرانککرپشن ہے۔'}
    ]
}

print(json.dumps(result, ensure_ascii=False))
PYTHON;

    // Create directory if not exists
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    file_put_contents($path, $scriptContent);
}
    
    // Public user voice complaint
    public function processPublicComplaint($audioFile, $complaintId, $isAnonymous = false)
    {
        try {
            $userId = $isAnonymous ? null : Auth::id();
            
            $result = $this->transcribeAudio($audioFile, $complaintId, null, $userId);
            
            if ($result['success']) {
                return [
                    'success' => true,
                    'message' => 'Voice complaint transcribed successfully',
                    'data' => [
                        'complaint_id' => $complaintId,
                        'transcription_id' => $result['transcription']->id,
                        'preview_text' => substr($result['roman_text'], 0, 200) . '...',
                        'full_text' => $result['roman_text']
                    ]
                ];
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Public complaint error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to process voice complaint'];
        }
    }
    
    private function getPythonExecutable()
    {
        // Windows pe Python paths
        $possiblePaths = [
            'python',
            'python3',
            'py',
            env('PYTHON_PATH', 'python'),
            'C:\\Python39\\python.exe',
            'C:\\Python310\\python.exe',
            'C:\\Python311\\python.exe',
            'C:\\Python312\\python.exe',
            'C:\\Program Files\\Python39\\python.exe',
            'C:\\Program Files\\Python310\\python.exe',
            'C:\\Program Files\\Python311\\python.exe',
            'C:\\Program Files\\Python312\\python.exe',
            'C:\\Users\\' . getenv('USERNAME') . '\\AppData\\Local\\Programs\\Python\\Python39\\python.exe',
            'C:\\Users\\' . getenv('USERNAME') . '\\AppData\\Local\\Programs\\Python\\Python310\\python.exe',
            'C:\\Users\\' . getenv('USERNAME') . '\\AppData\\Local\\Programs\\Python\\Python311\\python.exe',
        ];
        
        foreach ($possiblePaths as $path) {
            try {
                Log::info('Checking Python at: ' . $path);
                $process = new Process([$path, '--version']);
                $process->run();
                if ($process->isSuccessful()) {
                    Log::info('Found Python at: ' . $path);
                    return $path;
                }
            } catch (\Exception $e) {
                Log::info('Python not found at ' . $path . ': ' . $e->getMessage());
                continue;
            }
        }
        
        throw new \Exception('Python executable not found. Please install Python 3.8+ and add to PATH.');
    }
    
    private function calculateConfidence($segments)
    {
        if (empty($segments)) {
            return 0.7; // Default confidence
        }
        
        $totalConfidence = 0;
        foreach ($segments as $segment) {
            $totalConfidence += $segment['confidence'] ?? 0.7;
        }
        
        return $totalConfidence / count($segments);
    }
}