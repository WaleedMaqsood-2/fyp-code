@extends('Public User.layouts.app')

@push('styles')
<link href="{{ asset('css/public user/complaints-form.css') }}" rel="stylesheet">
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
    <div class="container py-5">
      <div class="mb-4 border-bottom pb-3">
        <h1 class="fs-2 fw-bold">File a New Complaint</h1>
        <p class="text-muted">Please provide the details of the incident below.</p>
      </div>

      <!-- ✅ Complaint Form -->
      <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
          <!-- Left Column -->
          <div class="col-md-6">
            <div class="mb-3">
              <label for="subject" class="form-label">Subject</label>
              <input type="text" class="form-control" id="subject" name="subject"
                     placeholder="e.g., Theft of personal belongings" required>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea id="description" name="description" class="form-control" rows="5"
                        placeholder="Describe the incident in detail..." required></textarea>
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

          <!-- Right Column -->
          <div class="col-md-6">
            <label class="form-label">Upload Evidence</label>
            <div class="upload-box text-center p-4 border rounded">
              <span class="material-symbols-outlined fs-1 text-secondary">upload_file</span>
              <div class="mt-2">
                <label for="file-upload" class="btn btn-link text-decoration-none text-primary fw-bold">
                  Upload a file
                  <input type="file" id="file-upload" name="evidence" class="d-none">
                </label>
                <span class="text-muted">or drag and drop</span>
              </div>
              <p class="small text-muted mt-1">Size up to 10MB</p>
            </div>
          </div>
        </div>

        <!-- Submit -->
        <div class="text-end mt-4">
          <button type="submit" class="btn btn-primary-custom text-white px-4 py-2">
            {{-- <span class="material-symbols-outlined">send</span> --}}
            Submit Complaint
          </button>
        </div>
      </form>
    </div>
  </main>
</div>
@endsection
