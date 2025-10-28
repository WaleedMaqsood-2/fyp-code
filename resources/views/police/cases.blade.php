@extends('police.layouts.main')
@section('title', 'Complaint Management - Police Module')
<link rel="stylesheet" href="{{ asset('css/police/cases.css') }}">

@section('content')
<div class="container ">
       @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        
    @endif
    @if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif

  <h2 class="fw-bold text-primary mb-4">Public Complaints Inbox</h2>

  <div class="card">
    <div class="card-body">
      <h5 class="text-primary mb-3">
        <span class="material-icons align-middle">report_problem</span> Public Complaints
      </h5>
  

       {{-- 🔍 Filters --}}
<div style="margin-bottom: 20px; padding: 10px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 5px; width: 100%;">
        
        {{-- 🔍 Search --}}
        <input 
            type="search" 
            id="complaintSearch" 
            name="search" 
            placeholder="Search by Id ..." 
            value="{{ request('search') }}"  {{-- 👈 keeps previous value --}}
            style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; flex: 1; min-width: 50px;"
        >

        {{-- 🟡 Status --}}
        <select name="status" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px;">
            <option value="">All Statuses</option>
            <option value="received" {{ request('status')=='received' ? 'selected' : '' }}>Received</option>
            <option value="under_review" {{ request('status')=='under_review' ? 'selected' : '' }}>Under Review</option>
            <option value="resolved" {{ request('status')=='resolved' ? 'selected' : '' }}>Resolved</option>
        </select>

        {{-- 📅 Date --}}
        <input 
            type="date" 
            name="date" 
            value="{{ request('date') }}" 
            style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px;"
        >

        {{-- 🔵 Incident Type --}}
        <select name="type" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px;">
            <option value="" selected disabled>All Types</option>
            @foreach ($incidentTypes as $type)
                <option value="{{ $type }}" {{ request('type')==$type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>

        {{-- ⬆️ Sort Order --}}
        <select name="sort_order" style="padding:6px 10px; border:1px solid #d1d5db; border-radius:6px;">
            <option value="asc" {{ request('sort_order')=='asc' ? 'selected' : '' }}>Ascending</option>
            <option value="desc" {{ request('sort_order')=='desc' ? 'selected' : '' }}>Descending</option>
        </select>

        {{-- 🔘 Filter Button --}}
        <button 
            type="submit" 
            style="padding: 6px 12px; background: #0ea5e9; color: #fff; border-radius: 6px; border: none;">
            Filter
        </button>
        {{-- 🔄 Reset Button --}}
        <a 
            href="{{ route('police.cases') }}" 
            style="padding: 6px 12px; background: #6b7280; color: #fff; border-radius: 6px; border: none; text-decoration: none;">
            Reset
        </a>

    </form>
</div>

     <!-- ASSIGNED CASES TABLE -->
  <div class="card shadow-sm border-0 mb-5 rounded-4">
    <div class="table-responsive">
      <table class="table align-middle mb-0" id="complaintTable">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Track ID</th>
            <th>Complaint By</th>
            <th>Title</th>
            <th>Incident Type</th>
            <th>Status</th>
            <th>Severity</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cases as $case)
          <tr >
            <td>{{ $loop->iteration }}</td>
            <td>{{ $case->track_id }}</td>
            <td>{{ $case->user?->name ?? 'N/A' }}</td>
            <td>{{ $case->subject }}</td>
            <td>{{ $case->incident_type }}</td>
            <td>
              @if($case->status == 'under_review')
                <span class="badge bg-warning text-dark">Under Review</span>
              @elseif($case->status == 'received')
                <span class="badge bg-success">Received</span>
              @elseif($case->status == 'resolved')
                <span class="badge bg-secondary">Closed</span>
              @else
                <span class="badge bg-info text-dark">{{ $case->status }}</span>
              @endif
            </td>
            <td>
              @if($case->severity == 'High')
                <span class="badge bg-danger">High</span>
              @elseif($case->severity == 'Medium')
                <span class="badge bg-warning text-dark">Medium</span>
              @else
                <span class="badge bg-success">Low</span>
              @endif
            </td>
            <td>{{ $case->created_at->format('Y-m-d') }}</td>
          <td>
  <!-- View Details Button -->
  <a href="#" class="text-primary fw-bold text-decoration-none" 
     data-bs-toggle="modal" data-bs-target="#viewCaseModal{{ $case->id }}">
     View Details
  </a>
</td>

          </tr>
          <tr id="noComplaintRow" style="display:none;">
  <td colspan="9" class="text-center text-muted">No complaints found.</td>
</tr>

          @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No cases assigned yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>



      @foreach($cases as $case)
  <!-- View Details Modal -->
  <div class="modal fade" id="viewCaseModal{{ $case->id }}" tabindex="-1" aria-labelledby="viewCaseLabel{{ $case->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-4 shadow-lg">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold" id="viewCaseLabel{{ $case->id }}">Case #{{ $case->id }} Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6 mb-3">
              <h6 class="text-muted mb-1">Track Id:</h6>
              <p class="fw-semibold">{{ $case->track_id }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <h6 class="text-muted mb-1">Title:</h6>
              <p class="fw-semibold">{{ $case->subject }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <h6 class="text-muted mb-1">Status:</h6>
              <p>
                @if($case->status == 'under_review')
                  <span class="badge bg-warning text-dark">Under Review</span>
                @elseif($case->status == 'received')
                  <span class="badge bg-success">Received</span>
                @elseif($case->status == 'resolved')
                  <span class="badge bg-secondary">Closed</span>
                @else
                  <span class="badge bg-info text-dark">{{ ucfirst($case->status) }}</span>
                @endif
              </p>
            </div>
        
            <div class="col-md-6 mb-3">
              <h6 class="text-muted mb-1">Severity:</h6>
              <p>
                @if($case->severity == 'High')
                  <span class="badge bg-danger">High</span>
                @elseif($case->severity == 'Medium')
                  <span class="badge bg-warning text-dark">Medium</span>
                @else
                  <span class="badge bg-success">Low</span>
                @endif
              </p>
            </div>
            <div class="col-md-6 mb-3">
              <h6 class="text-muted mb-1">Date Reported:</h6>
              <p class="fw-semibold">{{ $case->created_at->format('Y-m-d H:i') }}</p>
            </div>
         

         

          @if($case->location)
          <div class="mb-3 col-md-6">
            <h6 class="text-muted mb-1">Location:</h6>
            <p class="fw-normal">{{ $case->location }}</p>
          </div>
          @endif

          @if($case->note)
          <div class="mb-3 col-md-6">
            <h6 class="text-muted mb-1">Police Note:</h6>
            <p class="fw-normal">{{ $case->note }}</p>
          </div>
          @endif
          @if($case->incident_type)
          <div class="mb-3 col-md-6">
            <h6 class="text-muted mb-1">Incident Type:</h6>
            <p class="fw-normal">{{ $case->incident_type }}</p>
          </div>
          @endif
          @if ($case->description)
            
          <div class="mb-3 col-md-6">
            <h6 class="text-muted mb-1">Description:</h6>
            <p class="fw-normal">{{ $case->description }}</p>
          </div>
          @endif
          @if ( $case->transcription )
            
          <div class="mb-3 col-md-6">
            <h6 class="text-muted mb-1">Transcription:</h6>
            <p class="fw-normal">{{ $case->transcription }}</p>
          </div>
          @endif
        </div>
        
      </div>


          <!-- Evidence -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Attached Evidence</h6>
                    <div class="row g-3">
                        @forelse($case->media as $media)
                            <div class="col-6 col-sm-4 col-md-3">
                                <a href="{{ asset('storage/'.$media->file_path) }}" target="_blank" class="d-block text-decoration-none text-center">
                                    <div class="border rounded p-2 bg-light d-flex align-items-center justify-content-center" style="height:100px;">
                                       @php
    $ext = strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION));
@endphp

@if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
    {{-- Image preview --}}
    <img src="{{ asset('storage/'.$media->file_path) }}" alt="Evidence" class="img-fluid h-100 object-fit-contain">

@elseif(in_array($ext, ['mp4','mov','avi','mkv']))
    {{-- Video icon --}}
    <i class="bi bi-camera-video fs-2 text-secondary"></i>

@elseif(in_array($ext, ['mp3','wav','aac']))
    {{-- Audio icon --}}
    <i class="bi bi-music-note-beamed fs-2 text-secondary"></i>

@elseif(in_array($ext, ['zip','rar']))
    {{-- Archive icon --}}
    <i class="bi bi-file-zip fs-2 text-secondary"></i>

@elseif(in_array($ext, ['pdf']))
    {{-- PDF icon --}}
    <i class="bi bi-filetype-pdf fs-2 text-danger"></i>

@elseif(in_array($ext, ['doc','docx']))
    {{-- Word icon --}}
    <i class="bi bi-filetype-docx fs-2 text-primary"></i>

@elseif(in_array($ext, ['xls','xlsx']))
    {{-- Excel icon --}}
    <i class="bi bi-filetype-xlsx fs-2 text-success"></i>

@elseif(in_array($ext, ['txt']))
    {{-- Text icon --}}
    <i class="bi bi-file-text fs-2 text-secondary"></i>

@else
    {{-- Fallback icon for unknown file types --}}
    <i class="bi bi-file-earmark fs-2 text-secondary"></i>
@endif

</div>
<small class="d-block mt-2 text-truncate">{{ basename($media->file_path) }}</small>
</a>
</div>

                        @empty
                            <p class="text-muted">No evidence attached.</p>
                            
                        @endforelse
                           {{-- 🔹 Show audio file (if stored separately in column) --}}
      @if(!empty($case->audio_file))
        <div class="col-12 mt-3">
          <h6 class="fw-bold mb-2">Audio Evidence:</h6>
          <audio controls class="w-100">
            <source src="{{ asset('storage/'.$case->audio_file) }}" type="audio/{{ pathinfo($case->audio_file, PATHINFO_EXTENSION) }}">
            Your browser does not support the audio element.
          </audio>
          <small class="text-muted d-block mt-1">{{ basename($case->audio_file) }}</small>
        </div>
      @endif
                    </div>
                </div>
            </div>
        

       <div class="modal-footer">
  <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
  
  <!-- Edit Button -->
  <button class="btn btn-primary" data-bs-toggle="modal" 
          data-bs-target="#editCaseModal{{ $case->id }}" 
          data-bs-dismiss="modal">
    Edit Case
  </button>

  <!-- ✅ Forward to Forensic Analyst -->
  <form action="" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-success">
      <i class="bi bi-send"></i> Forward to Forensic Analyst
    </button>
  </form>
</div>

      </div>
    </div>
  </div>

  <!-- Edit Case Modal -->
  <div class="modal fade" id="editCaseModal{{ $case->id }}" tabindex="-1" aria-labelledby="editCaseLabel{{ $case->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-4 shadow-lg">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title fw-bold" id="editCaseLabel{{ $case->id }}">Edit Case #{{ $case->id }}</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('police.cases.update', $case->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="received" {{ $case->status == 'received' ? 'selected' : '' }}>Received</option>
                <option value="under_review" {{ $case->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                <option value="resolved" {{ $case->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Police Note / Update</label>
              <textarea name="note" class="form-control" rows="4">{{ $case->note }}</textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach

    </div>

      
     <div class="card-footer border-0 bg-white d-flex justify-content-between align-items-center">
  {{-- View All / Show Less Button --}}
  @if(request()->has('show_all'))
      <a href="{{ route('police.cases') }}" class="btn btn-outline-primary fw-bold px-4 shadow-sm">
          <i class="bi bi-eye-slash"></i> Show Less
      </a>
  @else
      <a href="{{ route('police.cases', ['show_all' => true]) }}" class="btn btn-primary fw-bold px-4 shadow-sm ">
          <i class="bi bi-eye"></i> View All
      </a>
  @endif
  {{-- Pagination --}}
  @unless(request()->has('show_all'))
      <div class="mt-2">
          {{ $cases->links('pagination::bootstrap-5') }}
      </div>
  @endunless

</div>

   
  </div>
    </div>
  </div>
</div>
@endsection


