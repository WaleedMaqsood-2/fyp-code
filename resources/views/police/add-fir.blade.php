@extends('police.layouts.main')
<style>
body {
  background: #f5f7fa;
}
.page-header {
  border-bottom: 2px solid #dee2e6;
  margin-bottom: 1.5rem;
}
.record-btn {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #dc3545;
  color: white;
  border: none;
  font-size: 1.5rem;
  transition: background 0.3s;
}
.record-btn.recording {
  background-color: #198754;
  animation: pulse 1.5s infinite;
}
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(25,135,84,0.7); }
  70% { box-shadow: 0 0 0 10px rgba(25,135,84,0); }
  100% { box-shadow: 0 0 0 0 rgba(25,135,84,0); }
}
</style>
@section('title', 'File FIR - Police Module')
@section('content')
<div class="container py-4">
  <div class="page-header">
    <h2 class="fw-bold text-primary">File New FIR</h2>
    <p class="text-muted">Manually enter FIR details or record a voice complaint for AI transcription.</p>
  </div>

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
  </form>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.js"></script>
<script>
let mediaRecorder, audioChunks = [];

const recordBtn = document.getElementById('recordBtn');
const recordStatus = document.getElementById('recordStatus');
const audioPlayback = document.getElementById('audioPlayback');
const transcribeBtn = document.getElementById('transcribeBtn');

recordBtn.addEventListener('click', async () => {
  if (!mediaRecorder || mediaRecorder.state === 'inactive') {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      mediaRecorder = new MediaRecorder(stream);
      audioChunks = [];
      mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
      mediaRecorder.onstop = () => {
        const audioBlob = new Blob(audioChunks, { type: 'audio/mp3' });
        const audioUrl = URL.createObjectURL(audioBlob);
        audioPlayback.src = audioUrl;
        audioPlayback.classList.remove('d-none');
        transcribeBtn.disabled = false;
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

// Mock AI Transcription (frontend only)
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

// Submit FIR mock
document.getElementById('firForm').addEventListener('submit', (e) => {
  e.preventDefault();
  Swal.fire('Success', 'FIR filed successfully!', 'success');
});
</script>
</body>
</html>
