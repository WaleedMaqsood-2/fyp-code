<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class OfflineWhisperService
{
    public function transcribeAudio($audioPath, $lang = 'ur')
    {
        $python = env('PYTHON_PATH', 'python'); // windows: 'python' or 'python3'
        $script = base_path('app/PythonScripts/transcribe_offline.py');

        $process = new Process([$python, $script, $audioPath, $lang]);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            return [
                'success' => false,
                'error' => $process->getErrorOutput()
            ];
        }

        $output = trim($process->getOutput());
        $parts = explode('||', $output);

        return [
            'success' => true,
            'urdu_text' => $parts[0] ?? '',
            'roman_text' => $parts[1] ?? '',
            'confidence' => isset($parts[2]) ? (float)$parts[2] : 0.5
        ];
    }
}
