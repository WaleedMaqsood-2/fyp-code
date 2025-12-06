<?php
// app/Services/TranscriptionService.php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;

class TranscriptionService
{
    protected $pythonPath;
    protected $scriptPath;
    
    public function __construct()
    {
        // Windows paths
        $this->pythonPath = 'python'; // یا 'D:\Python\python.exe'
        
        // Try different script versions
        $script1 = base_path('app/PythonScripts/transcribe.py');
        $script2 = base_path('app/PythonScripts/transcribe.py');
        
        // Use fixed version if exists, otherwise original
        $this->scriptPath = file_exists($script1) ? $script1 : $script2;
    }
    
    public function transcribeAudio($audioPath, $language = 'ur')
    {
        Log::info('TranscriptionService started', [
            'audio_path' => $audioPath,
            'language' => $language,
            'script_path' => $this->scriptPath,
            'script_exists' => file_exists($this->scriptPath)
        ]);
        
        if (!file_exists($audioPath)) {
            Log::error('Audio file not found: ' . $audioPath);
            return $this->errorResponse('آڈیو فائل نہیں ملی: ' . basename($audioPath));
        }
        
        if (!file_exists($this->scriptPath)) {
            Log::error('Python script not found: ' . $this->scriptPath);
            return $this->errorResponse('Python script not found');
        }
        
        // Command بنائیں
        $command = [
            $this->pythonPath,
            $this->scriptPath,
            $audioPath,
            $language
        ];
        
        Log::info('Running command: ' . implode(' ', $command));
        
        $process = new Process($command);
        $process->setTimeout(300);
        $process->setIdleTimeout(180);
        
        try {
            $process->mustRun();
            
            $output = trim($process->getOutput());
            $errorOutput = trim($process->getErrorOutput());
            
            Log::debug('Python stdout: ' . substr($output, 0, 500));
            Log::debug('Python stderr: ' . $errorOutput);
            
            if (empty($output)) {
                Log::error('Empty output from Python script');
                return $this->errorResponse('Python script returned empty response');
            }
            
            // Parse output
            $result = $this->parseTranscriptionOutput($output);
            
            // If confidence is very low (0.1), there might be an error
            if (isset($result['confidence']) && $result['confidence'] <= 0.1) {
                Log::warning('Low confidence transcription', $result);
            }
            
            Log::info('Transcription completed', [
                'success' => $result['success'] ?? false,
                'confidence' => $result['confidence'] ?? 0
            ]);
            
            return $result;
            
        } catch (ProcessFailedException $exception) {
            $error = $exception->getMessage();
            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();
            
            Log::error('Python process failed', [
                'error' => $error,
                'stdout' => $output,
                'stderr' => $errorOutput,
                'exit_code' => $process->getExitCode()
            ]);
            
            return $this->errorResponse('Python process failed: ' . $errorOutput);
        }
    }
    
    private function parseTranscriptionOutput($output)
    {
        try {
            $data = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return $data;
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse transcription output', ['error' => $e->getMessage()]);
        }
        
        return $this->errorResponse('Failed to parse transcription output');
    }
    
    private function errorResponse($message)
    {
        return [
            'success' => false,
            'error' => $message,
            'text' => '',
            'confidence' => 0
        ];
    }
}