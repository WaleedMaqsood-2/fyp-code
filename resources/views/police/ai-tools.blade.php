@extends('police.layouts.main')
@section('title', 'AI Tools - Police Module')

<style>
body {
  background-color: #f5f7fa;
}
.card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.section-title {
  color: #0d6efd;
  font-weight: 600;
}
textarea {
  resize: none;
}
.preview-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
}
.face-box {
  border: 2px dashed #0d6efd;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  background: #f9fbff;
}
.face-box:hover {
  background: #eaf2ff;
}
</style>

@section('content')

<div class="container py-5">
  <h2 class="fw-bold text-primary mb-4">AI Tools</h2>

  <!-- Transcription Tool -->
  <div class="card mb-5">
    <div class="card-body">
      <h5 class="section-title mb-3"><span class="material-icons align-middle">graphic_eq</span> Voice-to-Text Transcription</h5>
      <div class="row g-3 align-items-end">
        <div class="col-md-5">
          <label class="form-label">Upload Audio File</label>
          <input type="file" class="form-control" accept="audio/*">
        </div>
        <div class="col-md-3">
          <label class="form-label">Case ID (optional)</label>
          <input type="text" class="form-control" placeholder="e.g. P-103">
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100">
            <span class="material-icons">mic</span> Transcribe
          </button>
        </div>
      </div>

      <div class="mt-4">
        <label class="form-label">Transcription Result</label>
        <textarea class="form-control" rows="6" placeholder="AI-generated transcription will appear here..."></textarea>
        <div class="d-flex justify-content-end mt-3">
          <button class="btn btn-success">
            <span class="material-icons">save</span> Save to Case
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Face Detection Tool -->
  <div class="card">
    <div class="card-body">
      <h5 class="section-title mb-3"><span class="material-icons align-middle">face_retouching_natural</span> Face Detection & Matching</h5>

      <div class="row g-3 align-items-end">
        <div class="col-md-5">
          <div class="face-box">
            <span class="material-icons fs-1 text-primary">upload</span>
            <p class="text-muted mt-2 mb-0">Upload Image / Video for Face Detection</p>
            <input type="file" class="form-control mt-3" accept="image/*,video/*">
          </div>
        </div>

        <div class="col-md-3">
          <label class="form-label">Select Matching Source</label>
          <select class="form-select">
            <option>Criminal Database</option>
            <option>Recent FIRs</option>
            <option>Forensic Reports</option>
          </select>
        </div>

        <div class="col-md-2">
          <button class="btn btn-primary w-100">
            <span class="material-icons">search</span> Detect Faces
          </button>
        </div>
      </div>

      <div class="mt-4">
        <h6 class="fw-semibold text-secondary mb-2">Detected Faces:</h6>
        <div class="row g-3">
          <div class="col-md-3">
            <img src="{{ asset('assets/images/face1.jpg') }}" alt="Face 1" class="preview-img">
            <small class="d-block text-muted mt-1">Match: 95% (John Doe)</small>
          </div>
          <div class="col-md-3">
            <img src="{{ asset('assets/images/face2.jpg') }}" alt="Face 2" class="preview-img">
            <small class="d-block text-muted mt-1">No match found</small>
          </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
          <button class="btn btn-success">
            <span class="material-icons">save</span> Attach Results to Case
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
