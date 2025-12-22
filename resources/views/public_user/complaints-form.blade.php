@extends('public_user.layouts.app')

@push('styles')
<link href="{{ asset('css/public_user/complaints-form.css') }}" rel="stylesheet">
<style>
    /* Voice Complaint Styles */
    .voice-complaint-section {
        border: 2px dashed #007bff;
        border-radius: 10px;
        padding: 20px;
        background-color: #f8f9fa;
        margin-bottom: 20px;
    }
    
    .recording-controls {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .recording-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        background-color: #dc3545;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .audio-visualizer {
        width: 100%;
        height: 60px;
        background: linear-gradient(180deg, #e9ecef, #f8f9fa);
        border-radius: 5px;
        overflow: hidden;
        margin: 10px 0;
    }
    
    .transcription-preview {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-top: 15px;
        max-height: 200px;
        overflow-y: auto;
    }
    
    .confidence-bar {
        height: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        margin: 5px 0;
        overflow: hidden;
    }
    
    .confidence-fill {
        height: 100%;
        background: linear-gradient(90deg, #dc3545, #ffc107, #28a745);
        border-radius: 4px;
        transition: width 0.5s;
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column min-vh-100 w-75 mx-auto">
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
    @endif
    
    <!-- Main -->
    <main class="flex-grow bg-white">
        <div class="container py-5 ">
            <div class="mb-4 border-bottom pb-3 text-center">
                <h1 class="fs-2 fw-bold">File a New Complaint</h1>
                <p class="text-muted">You can submit complaint via text or voice recording</p>
            </div>

            <!-- Complaint Form -->
            <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" id="complaintForm">
                @csrf

                <!-- Voice Complaint Section -->
                <div class="voice-complaint-section mb-4">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-microphone me-2"></i>Voice Complaint (Optional)
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="recording-controls">
                                <button type="button" class="btn btn-outline-primary" id="startRecording">
                                    <i class="fas fa-microphone me-2"></i>Start Recording
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="stopRecording" disabled>
                                    <i class="fas fa-stop me-2"></i>Stop Recording
                                </button>
                                <div id="recordingStatus" class="d-none">
                                    <span class="recording-indicator me-2"></span>
                                    <span class="text-danger">Recording...</span>
                                </div>
                            </div>
                            
                            <div class="audio-visualizer" id="audioVisualizer">
                                <!-- Audio visualization will appear here -->
                            </div>
                            
                            {{-- <div class="mt-3">
                                <label class="form-label">Or upload audio file:</label>
                                <input type="file" class="form-control" id="voiceFile" name="voice_complaint" accept="audio/*">
                                <small class="text-muted">Supported formats: MP3, WAV, M4A, OGG (Max 5MB)</small>
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-info" id="previewTranscription">
                                    <i class="fas fa-eye me-2"></i>Preview Transcription
                                </button>
                                <button type="button" class="btn btn-warning" id="clearRecording">
                                    <i class="fas fa-trash me-2"></i>Clear
                                </button>
                            </div> --}}
                        </div>
                        
                        {{-- <div class="col-md-6">
                            <div id="transcriptionContainer" class="d-none">
                                <h6 class="mb-2">Transcription Preview:</h6>
                                <div class="transcription-preview" id="transcriptionPreview">
                                    <!-- Transcription will appear here -->
                                </div>
                                
                                <div class="mt-2">
                                    <small class="text-muted">Confidence Level:</small>
                                    <div class="confidence-bar">
                                        <div class="confidence-fill" id="confidenceFill" style="width: 0%"></div>
                                    </div>
                                    <small id="confidenceText" class="text-muted">Not available</small>
                                </div>
                                
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" id="useTranscription" name="use_transcription">
                                    <label class="form-check-label" for="useTranscription">
                                        Use this transcription as complaint description
                                    </label>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    
                    <input type="hidden" name="audio_data" id="audioData">
                </div>

                <div class="row g-4 shadow-sm">
                    <!-- Left Column -->
                    <div class="col-md-6 ">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                   placeholder="e.g., Theft of personal belongings" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea id="description" name="description" class="form-control" rows="5"
                                      placeholder="Describe the incident in detail..." required></textarea>
                            <small class="text-muted">If you used voice complaint, this will be auto-filled</small>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location"
                                   placeholder="Enter the location of the incident">
                        </div>

                        <div class="mb-3">
                            <label for="incident-type" class="form-label">Type of Incident</label>
                            <select class="form-select" id="incident-type" name="incident_type">
                                <option value="">Select type...</option>
                                <option value="Theft">Theft</option>
                                <option value="Assault">Assault</option>
                                <option value="Vandalism">Vandalism</option>
                                <option value="Cybercrime">Cybercrime</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="severity" class="form-label">Severity</label>
                            <select class="form-select" id="severity" name="severity">
                                <option value="">Select severity...</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column - Upload Evidence -->
                    <div class="col-md-6">
                        <label class="form-label">Upload Evidence</label>
                        <div class="upload-box text-center p-4 border rounded">
                            <span class="material-symbols-outlined fs-1 text-secondary">upload_file</span>
                            <div class="mt-2">
                                <label for="evidence" class="btn btn-link text-decoration-none text-primary fw-bold">
                                    Upload files
                                </label>
                                <input type="file" id="evidence" name="evidence[]" class="d-none" multiple>
                                <span class="text-muted">or drag and drop</span>
                            </div>
                            <p class="small text-muted mt-1">Size up to 10MB each</p>

                            <ul id="file-list" class="list-unstyled mt-2 text-start small text-success"></ul>
                        </div>
                        
                        <!-- Audio Preview -->
                        <div class="mt-3" id="audioPreviewContainer" style="display: none;">
                            <label class="form-label">Audio Preview:</label>
                            <audio id="audioPreview" controls class="w-100"></audio>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-end mt-4 mb-4">
                    <button type="submit" class="btn btn-primary-custom text-white px-4 py-2" id="submitBtn">
                        <i class="fas fa-paper-plane me-2"></i>
                        Submit Complaint
                    </button>
                </div>
            </form>

            @auth
                @if(isset($complaints) && $complaints->isNotEmpty())
                <div class="mt-5">
                    <h3 class="fw-bold mb-3">Your Submitted Complaints</h3>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered align-middle text-center">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Track ID</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Assigned To</th>
                                    <th scope="col">Submitted On</th>
                                    <th scope="col">Transcription</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($complaints as $complaint)
                                <tr>
                                    <td class="fw-semibold">{{ $complaint->track_id }}</td>
                                    <td>{{ $complaint->subject }}</td>
                                    <td>
                                        <span class="badge px-3 py-2 
                                        @if($complaint->status == 'received') bg-secondary
                                            @elseif($complaint->status == 'under_review') bg-warning text-dark
                                            @elseif($complaint->status == 'resolved') bg-success
                                            @else bg-dark @endif">
                                            {{ ucfirst($complaint->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $complaint->assignedUser->name ?? 'Unassigned' }}</td>
                                    <td>{{ $complaint->created_at->format('F d, Y h:i A') }}</td>
                                    <td>
                                        @if($complaint->has_transcription)
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="viewTranscription({{ $complaint->id }})">
                                            <i class="fas fa-file-audio"></i> View
                                        </button>
                                        @else
                                        <span class="badge bg-secondary">No Audio</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('complaints.hide', $complaint->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this complaint from your view?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endauth
        </div>
    </main>
</div>

<!-- Transcription Modal -->
<div class="modal fade" id="transcriptionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transcription Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    Loading...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script>
document.addEventListener("DOMContentLoaded", function() {
    // File upload handling
    let evidenceInput = document.getElementById('evidence');
    let fileList = document.getElementById('file-list');

    evidenceInput.addEventListener('change', function(e){
        fileList.innerHTML = "";
        Array.from(e.target.files).forEach(file => {
            let li = document.createElement('li');
            li.textContent = file.name;
            fileList.appendChild(li);
        });
    });

    // Voice Complaint Variables
    let mediaRecorder;
    let audioChunks = [];
    let audioBlob = null;
    let audioContext;
    let analyser;
    let dataArray;
    let bufferLength;
    let canvas;
    let canvasCtx;
    let isRecording = false;

    // Start Recording
    document.getElementById('startRecording').addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 44100
                } 
            });
            
            mediaRecorder = new MediaRecorder(stream, {
                mimeType: 'audio/webm;codecs=opus'
            });
            
            // Setup audio visualization
            setupAudioVisualization(stream);
            
            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };
            
            mediaRecorder.onstop = () => {
                audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                const audioUrl = URL.createObjectURL(audioBlob);
                
                // Show audio preview
                const audioPreview = document.getElementById('audioPreview');
                audioPreview.src = audioUrl;
                document.getElementById('audioPreviewContainer').style.display = 'block';
                
                // Convert to base64 for form submission
                const reader = new FileReader();
                reader.readAsDataURL(audioBlob);
                reader.onloadend = function() {
                    document.getElementById('audioData').value = reader.result.split(',')[1];
                };
                
                // Stop visualization
                if (audioContext) {
                    audioContext.close();
                }
            };
            
            mediaRecorder.start();
            isRecording = true;
            
            // Update UI
            document.getElementById('startRecording').disabled = true;
            document.getElementById('stopRecording').disabled = false;
            document.getElementById('recordingStatus').classList.remove('d-none');
            
        } catch (error) {
            console.error('Error accessing microphone:', error);
            alert('Microphone access failed. Please check permissions.');
        }
    });

    // Stop Recording
    document.getElementById('stopRecording').addEventListener('click', () => {
        if (mediaRecorder && isRecording) {
            mediaRecorder.stop();
            isRecording = false;
            
            document.getElementById('startRecording').disabled = false;
            document.getElementById('stopRecording').disabled = true;
            document.getElementById('recordingStatus').classList.add('d-none');
            
            // Stop all tracks
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
        }
    });

    // Preview Transcription
    document.getElementById('previewTranscription').addEventListener('click', async () => {
        let formData = new FormData();
        let audioFile;
        
        // Check if we have recorded audio or uploaded file
        if (audioBlob) {
            audioFile = new File([audioBlob], 'recording.webm', { type: 'audio/webm' });
        } else {
            const voiceFileInput = document.getElementById('voiceFile');
            if (!voiceFileInput.files.length) {
                alert('Please record or upload an audio file first');
                return;
            }
            audioFile = voiceFileInput.files[0];
        }
        
        formData.append('voice_complaint', audioFile);
        formData.append('_token', '{{ csrf_token() }}');
        
        // Show loading
        document.getElementById('transcriptionPreview').innerHTML = 
            '<div class="text-center"><div class="spinner-border text-primary"></div><p>Transcribing...</p></div>';
        document.getElementById('transcriptionContainer').classList.remove('d-none');
        
        try {
            const response = await fetch('{{ route("complaints.preview.voice") }}', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show transcription
                document.getElementById('transcriptionPreview').innerHTML = 
                    `<div class="mb-2"><strong>Roman Urdu:</strong><br>${result.preview_text}</div>
                     <div><strong>Original Urdu:</strong><br>${result.original_text}</div>`;
                
                // Update confidence
                const confidence = Math.round((result.confidence || 0) * 100);
                document.getElementById('confidenceFill').style.width = confidence + '%';
                document.getElementById('confidenceText').textContent = confidence + '% Confidence';
                document.getElementById('confidenceText').className = 
                    confidence >= 80 ? 'text-success' : confidence >= 60 ? 'text-warning' : 'text-danger';
                
                // Auto-fill description if checkbox is checked
                if (document.getElementById('useTranscription').checked) {
                    document.getElementById('description').value = result.preview_text;
                }
            } else {
                document.getElementById('transcriptionPreview').innerHTML = 
                    `<div class="text-danger">Error: ${result.error}</div>`;
            }
        } catch (error) {
            console.error('Transcription error:', error);
            document.getElementById('transcriptionPreview').innerHTML = 
                '<div class="text-danger">Failed to generate transcription</div>';
        }
    });

    // Clear Recording
    document.getElementById('clearRecording').addEventListener('click', () => {
        audioChunks = [];
        audioBlob = null;
        document.getElementById('audioPreview').src = '';
        document.getElementById('audioPreviewContainer').style.display = 'none';
        document.getElementById('audioData').value = '';
        document.getElementById('transcriptionContainer').classList.add('d-none');
        document.getElementById('voiceFile').value = '';
    });

    // Use Transcription checkbox
    document.getElementById('useTranscription').addEventListener('change', function() {
        if (this.checked) {
            const previewText = document.getElementById('transcriptionPreview').textContent;
            document.getElementById('description').value = previewText;
        }
    });

    // Audio Visualization Setup
    // Audio Visualization Setup
function setupAudioVisualization(stream) {
    audioContext = new (window.AudioContext || window.webkitAudioContext)();
    analyser = audioContext.createAnalyser();
    const source = audioContext.createMediaStreamSource(stream);
    source.connect(analyser);

    analyser.fftSize = 256;
    bufferLength = analyser.frequencyBinCount;
    dataArray = new Uint8Array(bufferLength);

    const visualizer = document.getElementById('audioVisualizer');

    // Canvas create if not already created
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.width = visualizer.clientWidth;
        canvas.height = visualizer.clientHeight;
        visualizer.appendChild(canvas);
        canvasCtx = canvas.getContext('2d');
    }

    function draw() {
        if (!isRecording) return;

        requestAnimationFrame(draw);

        analyser.getByteFrequencyData(dataArray);

        canvasCtx.fillStyle = '#f8f9fa';
        canvasCtx.fillRect(0, 0, canvas.width, canvas.height);

        const barWidth = (canvas.width / bufferLength) * 1.5;
        let x = 0;

        for (let i = 0; i < bufferLength; i++) {
            const barHeight = dataArray[i] / 2;

            canvasCtx.fillStyle = '#007bff';
            canvasCtx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);

            x += barWidth + 1;
        }
    }

    isRecording = true;
    draw();
}


    function drawVisualizer() {
        if (!isRecording) return;
        
        requestAnimationFrame(drawVisualizer);
        analyser.getByteFrequencyData(dataArray);
        
        canvasCtx.fillStyle = 'rgb(248, 249, 250)';
        canvasCtx.fillRect(0, 0, canvas.width, canvas.height);
        
        const barWidth = (canvas.width / bufferLength) * 2.5;
        let barHeight;
        let x = 0;
        
        for(let i = 0; i < bufferLength; i++) {
            barHeight = dataArray[i] / 2;
            
            canvasCtx.fillStyle = `rgb(${barHeight + 100}, 50, 150)`;
            canvasCtx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
            
            x += barWidth + 1;
        }
    }

    // Form submission with loading state
    document.getElementById('complaintForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    });
});

// View Transcription for existing complaint
function viewTranscription(complaintId) {
    fetch(`/complaints/${complaintId}/transcription`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Original Text:</h6>
                            <div class="p-3 bg-light rounded mb-3">${data.transcription.original_text || 'Not available'}</div>
                        </div>
                        <div class="col-md-6">
                            <h6>Roman Urdu:</h6>
                            <div class="p-3 bg-light rounded mb-3">${data.transcription.roman_text || 'Not available'}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Status:</strong> ${data.transcription.status}</p>
                            <p><strong>Confidence:</strong> ${Math.round((data.transcription.confidence_score || 0) * 100)}%</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Created:</strong> ${new Date(data.transcription.created_at).toLocaleString()}</p>
                            ${data.verification ? 
                                `<p><strong>Verified:</strong> ${data.verification.approved ? 'Yes' : 'No'}</p>
                                 <p><strong>Verified By:</strong> ${data.verification.analyst ? data.verification.analyst.name : 'N/A'}</p>` 
                                : ''}
                        </div>
                    </div>
                `;
                document.getElementById('modalContent').innerHTML = content;
            } else {
                document.getElementById('modalContent').innerHTML = 
                    `<div class="alert alert-warning">${data.message}</div>`;
            }
            new bootstrap.Modal(document.getElementById('transcriptionModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalContent').innerHTML = 
                '<div class="alert alert-danger">Failed to load transcription</div>';
            new bootstrap.Modal(document.getElementById('transcriptionModal')).show();
        });
}
</script>
@endsection