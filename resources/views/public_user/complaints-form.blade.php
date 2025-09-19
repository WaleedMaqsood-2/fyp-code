@extends('public_user.layouts.app')

@push('styles')
<link href="{{ asset('css/public_user/complaints-form.css') }}" rel="stylesheet">
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

<!-- ✅ JS for showing file names -->
<script>
document.getElementById('evidence').addEventListener('change', function(e){
    let fileList = document.getElementById('file-list');
    fileList.innerHTML = ""; // purane file names clear karo

    Array.from(e.target.files).forEach(file => {
        let li = document.createElement('li');
        li.textContent = file.name;
        fileList.appendChild(li);
    });
});
</script>


        <!-- Submit -->
        <div class="text-end mt-4">
          <button type="submit" class="btn btn-primary-custom text-white px-4 py-2">
            {{-- <span class="material-symbols-outlined">send</span> --}}
            Submit Complaint
          </button>
        </div>
      </form>
    @if($complaints->isNotEmpty())
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif


    </div>
  </main>
</div>
@endsection
