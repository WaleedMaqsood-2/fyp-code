@extends('police.layouts.main')
@section('title', 'File FIR - Police Module')
<link rel="stylesheet" href="{{ asset('css/police/add-fir.css') }}">
@section('content')
<div class="container">
  <div class="shadow-sm rounded px-4 py-2">


  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

  <div class="page-header">
    <h2 class="fw-bold text-primary">File New FIR</h2>
    <p class="text-muted">Manually enter FIR details or record a voice complaint for AI transcription.</p>
  </div>


  <form id="firForm" method="POST" action="{{ route('police.store_fir') }}" enctype="multipart/form-data">
  @csrf
  <div class="row g-4">
    <div class="col-md-6">
      <label class="form-label fw-semibold">Complaint Subject</label>
      <input type="text" name="subject" class="form-control" placeholder="Enter complaint subject" required>
    </div>

    <div class="col-md-6">
      <label class="form-label fw-semibold">Select Severity</label>
      <select class="form-select" name="severity" id="severity" required>
        <option value="">Select severity</option>
        <option>Low</option>
        <option>Medium</option>
        <option>High</option>
      </select>
    </div>


   <div class="col-md-6">
  <label class="form-label fw-semibold">Incident Type</label>
  <select name="incident_type" class="form-select" required>
    <option value="" selected disabled>-- Select Incident Type --</option>
    @foreach ($incidentTypes as $type)
        <option value="{{ $type }}">{{ $type }}</option>
    @endforeach
  </select>
</div>



    <div class="col-md-6">
      <label class="form-label fw-semibold">Location</label>
      <input type="text" name="location" class="form-control" placeholder="Enter location or area">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">Incident Description</label>
      <textarea name="description" class="form-control" rows="4"  placeholder="Describe the incident in detail" required></textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">Date & Time of Incident</label>
      <input type="datetime-local" name="incident_datetime" class="form-control">
    </div>

    <div class="col-12">
  <label class="form-label fw-semibold">Voice Recording (Optional)</label>
  <div class="card p-3">
    <div class="d-flex align-items-center gap-3">
      <button type="button" id="recordBtn" class="record-btn">
        <i class="bi bi-mic-fill"></i>
      </button>
      <div>
        <p class="mb-1 fw-semibold" id="recordStatus">Click to start recording</p>
        <audio id="audioPlayback" controls class="d-none mt-2"></audio>
      </div>
      <button type="button" id="transcribeBtn" class="btn btn-outline-primary ms-auto" disabled>
        <i class="bi bi-robot"></i> Transcribe (AI)
      </button>
    </div>
  </div>
  
  <!-- Hidden input to hold recorded audio blob -->
  <input type="file" name="audio_file" id="audioFileInput" class="d-none" accept="audio/*">
  
  <textarea id="transcribedText" name="transcribedText" class="form-control mt-3" rows="3" placeholder="AI transcription will appear here..."></textarea>
</div>


    {{-- <div class="col-12">
      <label class="form-label fw-semibold">Upload Evidence Files (Optional)</label>
      <input type="file" name="evidence_files[]" class="form-control" multiple accept="image/*,video/*,audio/*,.pdf,.docx">
    </div> --}}

    <!-- ✅ Upload Evidence Section -->
<div class="col-md-6">
  <label class="form-label">Upload Evidence</label>
  <div class="upload-box text-center p-4 border rounded">
    <span class="material-symbols-outlined fs-1 text-secondary">upload_file</span>
    <div class="mt-2">
      <!-- Custom Upload Button -->
      <label for="evidence" class="btn btn-link text-decoration-none text-primary fw-bold">
        Upload files
      </label>
      <input type="file" id="evidence" name="evidence[]" class="d-none" multiple>
      <span class="text-muted">or drag and drop</span>
    </div>
    <p class="small text-muted mt-1">Size up to 10MB each</p>

    <!-- ✅ Selected file names will appear here -->
    <ul id="file-list" class="list-unstyled mt-2 text-start small text-success"></ul>
  </div>
</div>

    <div class="col-12 text-end">
      
      <button type="submit" class="btn btn-primary px-4">Submit FIR</button>
    </div>
  </div>
</form>
  </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  let mediaRecorder, audioChunks = [];

  const recordBtn = document.getElementById('recordBtn');
  const recordStatus = document.getElementById('recordStatus');
  const audioPlayback = document.getElementById('audioPlayback');
  const transcribeBtn = document.getElementById('transcribeBtn');
  const audioFileInput = document.getElementById('audioFileInput');

  recordBtn.addEventListener('click', async () => {
    if (!mediaRecorder || mediaRecorder.state === 'inactive') {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];

        mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
        mediaRecorder.onstop = async () => {
          const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
          const audioUrl = URL.createObjectURL(audioBlob);
          audioPlayback.src = audioUrl;
          audioPlayback.classList.remove('d-none');
          transcribeBtn.disabled = false;

          // Convert blob to File and attach to hidden input
          const audioFile = new File([audioBlob], "recording_" + Date.now() + ".webm", { type: "audio/webm" });
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(audioFile);
          audioFileInput.files = dataTransfer.files;
        };

        mediaRecorder.start();
        recordBtn.classList.add('recording');
        recordStatus.innerText = 'Recording... Click again to stop.';
      } catch (err) {
        Swal.fire('Error', 'Microphone access denied!', 'error');
      }
    } else {
      mediaRecorder.stop();
      recordBtn.classList.remove('recording');
      recordStatus.innerText = 'Recording stopped. You can play or transcribe.';
    }
  });

  // AI Transcription Mock
  transcribeBtn.addEventListener('click', () => {
    Swal.fire({
      title: 'Processing Audio...',
      text: 'Using AI to transcribe complaint (Whisper)',
      timer: 2000,
      didOpen: () => Swal.showLoading()
    }).then(() => {
      document.getElementById('transcribedText').value =
        "This is a sample transcription generated by the AI model (Whisper).";
    });
  });
});
</script>





<script>
document.addEventListener("DOMContentLoaded", function() {
    let evidenceInput = document.getElementById('evidence');
    let fileList = document.getElementById('file-list');

    evidenceInput.addEventListener('change', function(e){
        fileList.innerHTML = ""; // old names clear

        Array.from(e.target.files).forEach(file => {
          let li = document.createElement('li');
          li.textContent = file.name;
          fileList.appendChild(li);
        });
      });
    });
  </script>

@endpush
{{-- 
  <form id="firForm">
    <div class="row g-4">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Complaint Title</label>
        <input type="text" class="form-control" placeholder="Enter complaint title" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Complaint Category</label>
        <select class="form-select" required>
          <option value="">Select category</option>
          <option>Theft</option>
          <option>Assault</option>
          <option>Fraud</option>
          <option>Cybercrime</option>
          <option>Other</option>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label fw-semibold">Incident Description</label>
        <textarea class="form-control" rows="4" placeholder="Describe the incident in detail" required></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold">Location</label>
        <input type="text" class="form-control" placeholder="Enter location or area">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Date & Time of Incident</label>
        <input type="datetime-local" class="form-control">
      </div>

      <!-- Voice Recording Section -->
      <div class="col-12">
        <label class="form-label fw-semibold">Voice Recording (Optional)</label>
        <div class="card p-3">
          <div class="d-flex align-items-center gap-3">
            <button type="button" id="recordBtn" class="record-btn">
              <i class="bi bi-mic-fill"></i>
            </button>
            <div>
              <p class="mb-1 fw-semibold" id="recordStatus">Click to start recording</p>
              <audio id="audioPlayback" controls class="d-none mt-2"></audio>
            </div>
            <button type="button" id="transcribeBtn" class="btn btn-outline-primary ms-auto" disabled>
              <i class="bi bi-robot"></i> Transcribe (AI)
            </button>
          </div>
        </div>
        <textarea id="transcribedText" class="form-control mt-3" rows="3" placeholder="AI transcription will appear here..."></textarea>
      </div>

      <!-- Evidence Upload -->
      <div class="col-12">
        <label class="form-label fw-semibold">Upload Evidence Files (Optional)</label>
        <input type="file" class="form-control" multiple accept="image/*,video/*,audio/*,.pdf,.docx">
        <div class="form-text">You can upload multiple files such as photos, videos, or documents.</div>
      </div>

      <!-- Submit -->
      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary px-4">Submit FIR</button>
      </div>
    </div>
  </form> --}}