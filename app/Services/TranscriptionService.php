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
        $this->pythonPath = 'python';
        
        // Try different scripts in order
        $scripts = [
            'D:\web development\laravel\FYP\app\PythonScripts\transcribe_laravel.py',  // New Laravel-compatible
            'D:\web development\laravel\FYP\app\PythonScripts\transcribe.py',          // Original
            base_path('app/PythonScripts/transcribe_laravel.py'),                      // Alternative path
            base_path('app/PythonScripts/transcribe.py')                               // Alternative path
        ];
        
        foreach ($scripts as $script) {
            if (file_exists($script)) {
                $this->scriptPath = $script;
                Log::info('Using script: ' . $script);
                break;
            }
        }
        
        if (!$this->scriptPath) {
            throw new \Exception('No Python script found');
        }
    }
    
    public function transcribeAudio($audioPath, $language = 'ur')
    {
        Log::info('Transcription started', [
            'audio' => basename($audioPath),
            'language' => $language
        ]);
        
        if (!file_exists($audioPath)) {
            return $this->errorResponse('Audio file not found');
        }
        
        // Method 1: Try Process component
        $result = $this->tryProcessMethod($audioPath, $language);
        
        if ($result['success']) {
            return $result;
        }
        
        // Method 2: Try shell_exec
        Log::info('Trying shell_exec method');
        $result = $this->tryShellExecMethod($audioPath, $language);
        
        if ($result['success']) {
            return $result;
        }
        
        // Method 3: Direct CMD command
        Log::info('Trying direct CMD method');
        return $this->tryDirectCmdMethod($audioPath, $language);
    }
    
    private function tryProcessMethod($audioPath, $language)
    {
        $command = [
            $this->pythonPath,
            $this->scriptPath,
            $audioPath,
            $language,
            '--json' // Ensure JSON output
        ];
        
        Log::info('Process command: ' . implode(' ', $command));
        
       $process = new \Symfony\Component\Process\Process($command);
$process->setTimeout(300);
$process->run();
        


$output = $process->getOutput();
$data = json_decode($output, true);
        try {
            $process->mustRun();
            
            $output = trim($process->getOutput());
            $error = trim($process->getErrorOutput());
            
            Log::debug('Process stdout: ' . substr($output, 0, 200));
            if ($error) {
                Log::debug('Process stderr: ' . $error);
            }
            
            if (empty($output)) {
                return $this->errorResponse('Empty output from Python');
            }
            
            $parsed = $this->parseOutput($output);
            
            // Check if it's an error response
            if (strpos($output, 'ماڈل لوڈ نہیں ہو سکا') !== false || 
                strpos($output, 'Model load nahi ho saka') !== false) {
                Log::warning('Model load error detected');
                return $this->errorResponse('Model load failed in Process method');
            }
            
            return $parsed;
            
        } catch (ProcessFailedException $e) {
            Log::error('Process failed: ' . $e->getMessage());
            return $this->errorResponse('Process method failed');
        }
    }
    
    private function tryShellExecMethod($audioPath, $language)
    {
        $command = sprintf(
            'python "%s" "%s" %s',
            $this->scriptPath,
            $audioPath,
            $language
        );
        
        Log::info('Shell command: ' . $command);
        
        $output = shell_exec($command . ' 2>&1');
        
        if ($output === null) {
            return $this->errorResponse('shell_exec returned null');
        }
        
        $output = trim($output);
        
        if (empty($output)) {
            return $this->errorResponse('Empty output from shell_exec');
        }
        
        Log::debug('Shell output: ' . substr($output, 0, 200));
        
        $parsed = $this->parseOutput($output);
        
        // Check if it's an error response
        if (strpos($output, 'ماڈل لوڈ نہیں ہو سکا') !== false || 
            strpos($output, 'Model load nahi ho saka') !== false) {
            Log::warning('Model load error in shell_exec');
            return $this->errorResponse('Model load failed in shell_exec');
        }
        
        return $parsed;
    }
    
    private function tryDirectCmdMethod($audioPath, $language)
    {
        // This is the EXACT command that works in CMD
        $command = sprintf(
            'cd /d "D:\web development\laravel\FYP" && python "app\PythonScripts\transcribe.py" "%s" %s',
            $audioPath,
            $language
        );
        
        Log::info('Direct CMD command: ' . $command);
        
        $output = shell_exec($command . ' 2>&1');
        
        if ($output === null) {
            return $this->errorResponse('CMD command returned null');
        }
        
        $output = trim($output);
        
        if (empty($output)) {
            return $this->errorResponse('Empty output from CMD');
        }
        
        Log::debug('CMD output: ' . substr($output, 0, 200));
        
        return $this->parseOutput($output);
    }
    
    private function parseOutput($output)
    {
        $output = trim($output);
        
        // Split by delimiter
        $parts = explode('||', $output);
        
        if (count($parts) < 2) {
            // Not in expected format, return as Urdu text
            return [
                'success' => true,
                'urdu_text' => $output,
                'roman_text' => $this->convertToRoman($output),
                'confidence' => 0.7,
                'raw_output' => $output
            ];
        }
        
        $urduText = trim($parts[0]);
        $romanText = isset($parts[1]) ? trim($parts[1]) : '';
        $confidence = isset($parts[2]) ? floatval($parts[2]) : 0.5;
        
        // Validate confidence
        if ($confidence < 0 || $confidence > 1) {
            $confidence = 0.5;
        }
        
        // If no Roman text, generate it
        if (empty($romanText)) {
            $romanText = $this->convertToRoman($urduText);
        }
        
        return [
            'success' => true,
            'urdu_text' => $urduText,
            'roman_text' => $romanText,
            'confidence' => $confidence,
            'raw_output' => $output
        ];
    }
    
    private function convertToRoman($urduText)
    {
        $mapping = [
            'ا' => 'a', 'آ' => 'aa', 'ب' => 'b', 'پ' => 'p',
            'ت' => 't', 'ٹ' => 'tt', 'ث' => 's', 'ج' => 'j',
            'چ' => 'ch', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd',
            'ڈ' => 'dd', 'ذ' => 'z', 'ر' => 'r', 'ڑ' => 'rr',
            'ز' => 'z', 'ژ' => 'zh', 'س' => 's', 'ش' => 'sh',
            'ص' => 's', 'ض' => 'z', 'ط' => 't', 'ظ' => 'z',
            'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'q',
            'ک' => 'k', 'گ' => 'g', 'ل' => 'l', 'م' => 'm',
            'ن' => 'n', 'و' => 'w', 'ہ' => 'h', 'ھ' => 'h',
            'ی' => 'y', 'ے' => 'e', 'ں' => 'n'
        ];
        
        $roman = '';
        $length = mb_strlen($urduText);
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($urduText, $i, 1);
            $roman .= $mapping[$char] ?? $char;
        }
        
        return $roman;
    }
    
    private function errorResponse($message)
    {
        return [
            'success' => false,
            'error' => $message,
            'urdu_text' => $message,
            'roman_text' => 'Error: ' . $message,
            'confidence' => 0.0
        ];
    }
    
    /**
     * Direct test with CMD
     */
    public function testCmdDirect()
    {
        $testAudio = 'C:\Users\PMLS\Downloads\ghazals_ghalib_0809_librivox\aahkochaahyea_ghalib_64kb.mp3';
        
        if (!file_exists($testAudio)) {
            return ['error' => 'Test audio not found'];
        }
        
        $command = sprintf(
            'cd /d "D:\web development\laravel\FYP" && python "app\PythonScripts\transcribe.py" "%s" ur',
            $testAudio
        );
        
        Log::info('Direct CMD test: ' . $command);
        
        $output = shell_exec($command . ' 2>&1');
        
        return [
            'command' => $command,
            'output' => $output,
            'output_trimmed' => trim($output ?? '')
        ];
    }

    
    public function processPublicComplaint($audioFile, $complaintId, $isAnonymous = false)
    {
        try {
            // Process the audio file and return transcription result
            $transcriptionResult = $this->transcribeAudio($audioFile);
            
            return [
                'success' => true,
                'data' => [
                    'preview_text' => $transcriptionResult['roman_text'] ?? '',
                    'transcription_id' => $complaintId
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}