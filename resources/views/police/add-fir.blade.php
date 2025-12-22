@extends('police.layouts.main')
<link rel="stylesheet" href="{{ asset('css/police/add-fir.css') }}">
@section('title', 'File FIR - Police Module')



@section('content')
<div class="fir-container p-0 m-0">
    <!-- Header -->
    <div class="fir-header">
        <h1>
            <i class="fas fa-file-alt"></i>
            File New FIR
        </h1>
        <p>Manually enter FIR details or record a voice complaint.</p>
    </div>

    <!-- Alerts -->
    <div class="alert-section">
        @if(session('success'))
        <div class="alert-modern alert-success">
            <i class="fas fa-check-circle"></i>
            <div>
                <h6>Success</h6>
                <p>{{ session('success') }}</p>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
        
        @if($errors->any())
        <div class="alert-modern alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <h6>Error</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
    </div>

    <!-- Form -->
    <div class="form-container">
        <form id="firForm" method="POST" action="{{ route('police.store_fir') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-grid">
                <!-- Complaint Subject -->
                <div class="form-group">
                    <label class="form-label required">
                        <i class="fas fa-heading"></i>
                        Complaint Subject
                    </label>
                    <input type="text" 
                           name="subject" 
                           class="form-control" 
                           placeholder="e.g., Mobile Theft at Market Street"
                           required
                           value="{{ old('subject') }}">
                </div>

                <!-- Severity -->
                <div class="form-group">
                    <label class="form-label required">
                        <i class="fas fa-exclamation-triangle"></i>
                        Severity Level
                    </label>
                    <select name="severity" class="form-select" required>
                        <option value="">Select Severity Level</option>
                        <option value="Low" {{ old('severity') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('severity') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('severity') == 'High' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                <!-- Incident Type -->
                <div class="form-group">
                    <label class="form-label required">
                        <i class="fas fa-bullhorn"></i>
                        Incident Type
                    </label>
                    <select name="incident_type" class="form-select" required>
                        <option value="">Select Incident Type</option>
                        @foreach($incidentTypes as $type)
                        <option value="{{ $type }}" {{ old('incident_type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Incident Location
                    </label>
                    <input type="text" 
                           name="location" 
                           class="form-control" 
                           placeholder="e.g., Near Central Market, Sector 15"
                           value="{{ old('location') }}">
                </div>

                <!-- Description -->
                <div class="form-group full-width">
                    <label class="form-label required">
                        <i class="fas fa-align-left"></i>
                        Incident Description
                    </label>
                    <textarea name="description" 
                              class="form-textarea" 
                              placeholder="Describe the incident in detail..."
                              required>{{ old('description') }}</textarea>
                </div>

                <!-- Date & Time -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt"></i>
                        Date & Time of Incident
                    </label>
                    <input type="datetime-local" 
                           name="incident_datetime" 
                           class="form-control"
                           value="{{ old('incident_datetime') }}">
                </div>
            </div>

            <!-- Voice Recording Section -->
            <div class="voice-section">
                <h3 class="mb-3">
                    <i class="fas fa-microphone-alt"></i>
                    Voice Recording (Optional)
                </h3>
                
                <div class="voice-controls">
                    <button type="button" id="recordBtn" class="record-btn">
                        <i class="fas fa-microphone"></i>
                    </button>
                    
                    <div class="record-status">
                        <h6 id="recordStatus">Click to start recording</h6>
                        <p id="recordTimer" class="timer-display">00:00</p>
                    </div>
                    
                    <button type="button" id="clearRecording" class="btn btn-secondary" style="display: none;">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
                
                <div class="audio-player" id="audioPlayer" style="display: none;">
                    <h6>Recording Preview:</h6>
                    <audio id="audioPlayback" controls></audio>
                </div>
                
                <!-- Hidden input for audio file -->
                <input type="file" 
                       name="audio_file" 
                       id="audioFileInput" 
                       class="d-none" 
                       accept="audio/*">
            </div>

            <!-- Evidence Upload -->
            <div class="upload-section">
                <div class="upload-box">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h4>Upload Evidence Files</h4>
                    <p class="text-muted mb-3">Drag & drop files or click to browse</p>
                    
                    <label for="evidence" class="upload-btn">
                        <i class="fas fa-folder-open"></i>
                        Choose Files
                    </label>
                    
                    <input type="file" 
                           id="evidence" 
                           name="evidence[]" 
                           class="d-none" 
                           multiple 
                           accept="image/*,video/*,audio/*,.pdf,.docx,.doc,.xlsx,.txt">
                    
                    <p class="small text-muted mt-3">Maximum file size: 10MB each</p>
                </div>
                
                <!-- File List -->
                <div class="file-list" id="fileList"></div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-redo"></i>
                    Reset Form
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane"></i>
                    Submit FIR
                </button>
            </div>
        </form>
    </div>

    <!-- Similarity Results -->
    @if(session('match_result'))
    <div class="similarity-section">
        @php
            $match = session('match_result');
            $isWarning = $match['similarity'] >= 85;
        @endphp
        
        <div class="result-card {{ $isWarning ? 'warning' : 'success' }}">
            <div class="result-icon">
                <i class="fas {{ $isWarning ? 'fa-exclamation-triangle' : 'fa-check-circle' }}"></i>
            </div>
            <div class="result-content">
                @if($isWarning)
                <h4>⚠ Possible Similar Case Found</h4>
                <p>A similar case has been found in our database with {{ $match['similarity'] }}% similarity.</p>
                <p><strong>Matching Description:</strong> {{ $match['text'] }}</p>
                @else
                <h4>✅ No Similar Cases Found</h4>
                <p>Your FIR has been filed successfully. No similar cases detected in our database.</p>
                @endif
                
                @if($isWarning)
                <div class="similarity-meter">
                    <div class="meter-bar">
                        <div class="meter-fill" style="width: {{ $match['similarity'] }}%"></div>
                    </div>
                    <div class="similarity-percent">{{ $match['similarity'] }}%</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    @if(session('fir_id'))
    <div class="similarity-section">
        <div class="result-card success">
            <div class="result-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="result-content">
                <h4>✅ FIR Filed Successfully!</h4>
                <p>FIR #{{ session('fir_id') }} has been filed successfully with no similar cases detected.</p>
                <p><strong>Tracking ID:</strong> <span class="badge bg-primary">{{ session('tracking_id') ?? 'N/A' }}</span></p>
                <a href="{{ route('police.cases') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-eye"></i> View Case
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let mediaRecorder;
    let audioChunks = [];
    let recordingTimer;
    let seconds = 0;
    
    const recordBtn = document.getElementById('recordBtn');
    const recordStatus = document.getElementById('recordStatus');
    const recordTimer = document.getElementById('recordTimer');
    const audioPlayback = document.getElementById('audioPlayback');
    const audioPlayer = document.getElementById('audioPlayer');
    const audioFileInput = document.getElementById('audioFileInput');
    const clearRecordingBtn = document.getElementById('clearRecording');
    const submitBtn = document.getElementById('submitBtn');
    
    // File upload handling
    const evidenceInput = document.getElementById('evidence');
    const fileList = document.getElementById('fileList');
    
    evidenceInput.addEventListener('change', function(e) {
        fileList.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="file-info">
                    <i class="fas fa-file file-icon"></i>
                    <div>
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${formatFileSize(file.size)}</div>
                    </div>
                </div>
                <button type="button" class="file-remove" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    });
    
    // Remove file from list
    fileList.addEventListener('click', function(e) {
        if (e.target.closest('.file-remove')) {
            const index = e.target.closest('.file-remove').dataset.index;
            const files = Array.from(evidenceInput.files);
            files.splice(index, 1);
            
            const dataTransfer = new DataTransfer();
            files.forEach(file => dataTransfer.items.add(file));
            evidenceInput.files = dataTransfer.files;
            
            evidenceInput.dispatchEvent(new Event('change'));
        }
    });
    
    // Voice recording
    recordBtn.addEventListener('click', async function() {
        if (!mediaRecorder || mediaRecorder.state === 'inactive') {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true,
                        sampleRate: 44100
                    }
                });
                
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                seconds = 0;
                
                mediaRecorder.ondataavailable = event => {
                    audioChunks.push(event.data);
                };
                
                mediaRecorder.onstop = async function() {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    
                    audioPlayback.src = audioUrl;
                    audioPlayer.style.display = 'block';
                    clearRecordingBtn.style.display = 'block';
                    
                    // Create file from blob
                    const audioFile = new File([audioBlob], `recording_${Date.now()}.webm`, {
                        type: 'audio/webm'
                    });
                    
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(audioFile);
                    audioFileInput.files = dataTransfer.files;
                    
                    stopTimer();
                    recordBtn.classList.remove('recording');
                    recordStatus.textContent = 'Recording saved. Click to record again.';
                    recordBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                };
                
                mediaRecorder.start();
                startTimer();
                recordBtn.classList.add('recording');
                recordStatus.textContent = 'Recording... Click to stop';
                recordBtn.innerHTML = '<i class="fas fa-stop"></i>';
                
            } catch (error) {
                console.error('Error accessing microphone:', error);
                alert('Microphone access is required for recording. Please check your browser permissions.');
            }
        } else {
            mediaRecorder.stop();
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
        }
    });
    
    // Clear recording
    clearRecordingBtn.addEventListener('click', function() {
        audioPlayback.src = '';
        audioPlayer.style.display = 'none';
        this.style.display = 'none';
        audioFileInput.value = '';
        recordStatus.textContent = 'Click to start recording';
        recordTimer.textContent = '00:00';
    });
    
    // Timer functions
    function startTimer() {
        seconds = 0;
        updateTimer();
        recordingTimer = setInterval(() => {
            seconds++;
            updateTimer();
        }, 1000);
    }
    
    function stopTimer() {
        clearInterval(recordingTimer);
    }
    
    function updateTimer() {
        const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
        const secs = (seconds % 60).toString().padStart(2, '0');
        recordTimer.textContent = `${mins}:${secs}`;
    }
    
    // Form validation
    const firForm = document.getElementById('firForm');
    firForm.addEventListener('submit', function(e) {
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
    });
    
    // File size formatting
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Auto-hide alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-modern').forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.style.display = 'none', 300);
        });
    }, 5000);
    
    // Form auto-save (optional)
    const formData = new FormData();
    const formInputs = firForm.querySelectorAll('input, select, textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            localStorage.setItem('fir_draft', JSON.stringify({
                subject: firForm.subject.value,
                severity: firForm.severity.value,
                incident_type: firForm.incident_type.value,
                location: firForm.location.value,
                description: firForm.description.value,
                incident_datetime: firForm.incident_datetime.value
            }));
        });
    });
    
    // Load draft on page load
    const draft = localStorage.getItem('fir_draft');
    if (draft) {
        const draftData = JSON.parse(draft);
        Object.keys(draftData).forEach(key => {
            if (firForm[key]) {
                firForm[key].value = draftData[key];
            }
        });
    }
    
    // Clear draft on successful submit
    firForm.addEventListener('submit', function() {
        localStorage.removeItem('fir_draft');
    });
});
</script>
@endpush