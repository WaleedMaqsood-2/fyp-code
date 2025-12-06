<!DOCTYPE html>
<html>
<head>
    <title>Transcription Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Transcription Preview</h2>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="card">
            <div class="card-header">
                Transcription #{{ $transcription->id }}
                <span class="badge bg-{{ $transcription->status == 'completed' ? 'success' : 'warning' }}">
                    {{ $transcription->status }}
                </span>
            </div>
            
            <div class="card-body">
                <p><strong>Confidence:</strong> {{ round($transcription->confidence_score * 100) }}%</p>
                
                <div class="mb-3">
                    <h5>Urdu Text:</h5>
                    <div class="p-3 border bg-light" style="direction: rtl;">
                        {{ $transcription->original_text ?: 'No text available' }}
                    </div>
                </div>
                
                <div class="mb-3">
                    <h5>Roman Urdu:</h5>
                    <div class="p-3 border bg-light">
                        {{ $transcription->roman_text ?: 'No roman text' }}
                    </div>
                </div>
                
                @if($transcription->audio_path)
                <div class="mb-3">
                    <h5>Audio:</h5>
                    <audio controls class="w-100">
                        <source src="{{ Storage::url($transcription->audio_path) }}">
                        Your browser does not support audio.
                    </audio>
                </div>
                @endif
                
                <form method="POST" action="{{ route('transcription.save', $transcription->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label>Correct Urdu Text:</label>
                        <textarea name="corrected_text" class="form-control" rows="5">{{ $transcription->original_text }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Save Correction</button>
                </form>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="{{ route('transcription.form', $transcription->complaint_id) }}" class="btn btn-primary">Upload Another</a>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</body>
</html>