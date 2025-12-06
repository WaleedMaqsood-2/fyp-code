<?php

namespace App\Services;

use App\Models\Transcription;
use App\Models\TranscriptionVerification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class WhisperTranscriptionService
{
    public function transcribeAudio($audioFile, $complaintId, $mediaId, $language = 'ur')
    {
        try {
            // 1. Audio file save karein (Windows compatible path)
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\.]/', '_', $audioFile->getClientOriginalName());
            $path = $audioFile->storeAs('audio_uploads', $fileName, 'public');
            
            // Windows absolute path
            $fullPath = storage_path('app/public/' . $path);
            
            // Windows pe backslashes handle karein
            $fullPath = str_replace('/', DIRECTORY_SEPARATOR, $fullPath);
            
            // 2. Check file exists
            if (!file_exists($fullPath)) {
                throw new \Exception("Audio file not found at: " . $fullPath);
            }
        
            Log::info("Transcribing audio: " . $fullPath);
            
            // 3. Python script path (Windows compatible)
         $pythonScript = base_path('app/PythonScripts/transcribe.py');
$pythonScript = str_replace('/', '\\', $pythonScript);

            // 4. Python executable path (Windows)
            $pythonExecutable = $this->getPythonExecutable();
            
            // 5. Process create karein (Windows compatible)
            $process = new Process([
                $pythonExecutable,
                $pythonScript,
                $fullPath,
                $language
            ]);
            
            $process->setTimeout(600); // 10 minutes timeout (Windows pe thora slow ho sakta hai)
            $process->setIdleTimeout(300); // 5 minutes idle timeout
            
            Log::info("Starting transcription process...");
            $process->run();
            
            // 6. Check agar process successful hai
            if (!$process->isSuccessful()) {
                Log::error("Process failed: " . $process->getErrorOutput());
                throw new ProcessFailedException($process);
            }
            
            // 7. Result parse karein
            $output = $process->getOutput();
            Log::info("Python output: " . substr($output, 0, 500));
            
            $result = json_decode($output, true);
            
            if (!$result || !isset($result['success'])) {
                throw new \Exception('Invalid response from Python script: ' . $output);
            }
            
            if (!$result['success']) {
                throw new \Exception('Transcription failed: ' . ($result['error'] ?? 'Unknown error'));
            }
            
            // 8. Database mein save karein
            $transcription = Transcription::create([
                'complaint_id' => $complaintId,
                'media_id' => $mediaId,
                'original_transcript' => $result['original_text'],
                'roman_transcript' => $result['roman_text'],
                'audio_path' => $path,
                'language' => $result['language'] ?? 'ur',
                'status' => 'completed',
                'confidence_score' => $this->calculateConfidence($result['segments'] ?? []),
                'device_used' => $result['device_used'] ?? 'cpu'
            ]);
            
            // 9. Forensic analyst ke liye verification entry banayein
            $this->createVerificationEntry($transcription->id);
            
            Log::info("Transcription completed successfully for complaint ID: " . $complaintId);
            
            return [
                'success' => true,
                'transcription' => $transcription,
                'original_text' => $result['original_text'],
                'roman_text' => $result['roman_text']
            ];
            
        } catch (\Exception $e) {
            // Error handling
            Log::error('Transcription failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'file_path' => $fullPath ?? 'Not set'
            ];
        }
    }
    
    private function getPythonExecutable()
    {
        // Windows pe Python executable find karein
        $possiblePaths = [
            'python3',
            'python',
            'py',
            'C:\Python39\python.exe',
            'C:\Python310\python.exe',
            'C:\Python311\python.exe',
            'C:\Program Files\Python39\python.exe',
            'C:\Program Files\Python310\python.exe',
            'C:\Program Files\Python311\python.exe',
            env('PYTHON_PATH', 'python') // .env mein define kar saktay hain
        ];
        
        foreach ($possiblePaths as $path) {
            try {
                $process = new Process([$path, '--version']);
                $process->run();
                if ($process->isSuccessful()) {
                    Log::info("Found Python at: " . $path);
                    return $path;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        throw new \Exception('Python executable not found. Please install Python and add to PATH.');
    }
    
    private function calculateConfidence($segments)
    {
        if (empty($segments)) {
            return 0;
        }
        
        $totalConfidence = 0;
        foreach ($segments as $segment) {
            $totalConfidence += $segment['confidence'] ?? 0;
        }
        
        return $totalConfidence / count($segments);
    }
    
    private function createVerificationEntry($transcriptionId)
    {
        TranscriptionVerification::create([
            'transcription_id' => $transcriptionId,
            'analyst_id' => null,
            'is_approved' => false,
            'notes' => 'Pending verification'
        ]);
    }
}