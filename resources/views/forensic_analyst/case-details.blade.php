@extends('forensic_analyst.layouts.app')
@section('title','Case Details & Evidence Review')


@section('content')
<div class="container py-4">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-primary mb-0">
      <i class="material-icons align-middle">science</i> Case Details & AI Evidence Review
    </h3>
    <a href="{{ route('forensic.assigned-cases') }}" class="btn btn-outline-secondary">
      <i class="material-icons align-middle">arrow_back</i> Back to List
    </a>
  </div>

  <!-- Case Summary -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="material-icons align-middle">info</i> Case Summary</h5>
    </div>
    <div class="card-body">
      <p><strong>Complaint ID:</strong> #{{ $case->id }}</p>
      <p><strong>Title:</strong> {{ $case->title ?? 'N/A' }}</p>
      <p><strong>Filed By:</strong> {{ $case->user->name ?? 'Unknown' }}</p>
      <p><strong>Status:</strong> 
        <span class="badge bg-{{ $case->status == 'forwarded' ? 'warning' : 'success' }}">
          {{ ucfirst($case->status) }}
        </span>
      </p>
      <p><strong>Received On:</strong> {{ $case->created_at->format('d M Y, h:i A') }}</p>
    </div>
  </div>

  <!-- Evidence Review -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="material-icons align-middle">perm_media</i> Uploaded Evidence</h5>
      <div>
        <!-- AI Buttons -->
        {{-- <form action="{{ route('forensic.ai.faceDetect', $case->id) }}" method="POST" class="d-inline"> --}}
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-light"><i class="material-icons align-middle">face</i> Detect Faces</button>
        </form>
        {{-- <form action="{{ route('forensic.ai.transcribe', $case->id) }}" method="POST" class="d-inline"> --}}
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-light"><i class="material-icons align-middle">mic</i> Run Transcription</button>
        </form>
      </div>
    </div>
    <div class="card-body">
      @if($evidences->isEmpty())
        <p class="text-muted">No digital evidence uploaded for this case.</p>
      @else
        <div class="row">
          @foreach($evidences as $evidence)
            <div class="col-md-4 mb-3">
              <div class="card h-100 shadow-sm">
                @if(in_array(pathinfo($evidence->file_path, PATHINFO_EXTENSION), ['jpg','jpeg','png','gif']))
                  <img src="{{ asset('storage/'.$evidence->file_path) }}" class="card-img-top" alt="Evidence Image">
                @elseif(in_array(pathinfo($evidence->file_path, PATHINFO_EXTENSION), ['mp4','avi','mov']))
                  <video class="w-100" controls>
                    <source src="{{ asset('storage/'.$evidence->file_path) }}" type="video/mp4">
                  </video>
                @else
                  <div class="card-body text-center">
                    <i class="material-icons text-secondary" style="font-size:48px;">insert_drive_file</i>
                    <p class="mt-2">{{ basename($evidence->file_path) }}</p>
                    <a href="{{ asset('storage/'.$evidence->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                      View File
                    </a>
                  </div>
                @endif
                <div class="card-footer small text-muted">
                  Uploaded: {{ $evidence->created_at->format('d M Y') }}
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  <!-- AI Insights -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="material-icons align-middle">psychology</i> AI-Generated Insights</h5>
      {{-- <form action="{{ route('forensic.ai.summarize', $case->id) }}" method="POST"> --}}
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-light"><i class="material-icons align-middle">summarize</i> Generate Summary</button>
      </form>
    </div>
    <div class="card-body">
      <h6><strong>Transcription:</strong></h6>
      <p class="border p-2 bg-light">{{ $case->ai_transcription ?? 'No transcription available yet.' }}</p>

      <h6 class="mt-3"><strong>Summary:</strong></h6>
      <p class="border p-2 bg-light">{{ $case->ai_summary ?? 'No summary generated yet.' }}</p>
    </div>
  </div>

  <!-- Analyst Review -->
  <div class="card shadow-sm">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="material-icons align-middle">rate_review</i> Analyst Review</h5>
      <form action=
      {{-- "{{ route('forensic.ai.report', $case->id) }}" --}}
        method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-light"><i class="material-icons align-middle">description</i> Generate AI Report</button>
      </form>
    </div>
    <div class="card-body">
      {{-- <form action="{{ route('forensic.submitReview', $case->id) }}" method="POST"> --}}
        @csrf
        <div class="mb-3">
          <label for="notes" class="form-label">Your Analysis Notes</label>
          <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Enter your forensic findings..."></textarea>
        </div>

        <div class="mb-3">
          <label for="status" class="form-label">Update Case Status</label>
          <select name="status" id="status" class="form-select">
            <option value="analyzing">Analyzing</option>
            <option value="completed">Completed</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>

        <button type="submit" class="btn btn-success">
          <i class="material-icons align-middle">send</i> Submit Review
        </button>
      </form>
    </div>
  </div>

</div>
@endsection
