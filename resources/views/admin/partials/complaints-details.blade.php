@extends('layouts.master')

@push('styles')
<link href="{{ asset('css/admin/complaint-details.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="container py-4">
<div class="ms-2 mt-4">
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

    <!-- Back + Heading -->
    <div class="mb-4">
        <a href="{{ route('admin.complaints.index') }}" class="d-inline-flex align-items-center text-decoration-none">
            <i class="bi bi-arrow-left me-2"></i>
            <span class="text-muted">Back to Complaints</span>
        </a>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-12 col-lg-8">
            <!-- Complaint Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
                <div>
                    <h5 class="fw-bold mb-1">
                        Complaint Details: <span class="text-muted fw-semibold">#{{ $complaint->id }}</span>
                    </h5>
                    <small class="text-muted">
                        Submitted by <strong>{{ $complaint->user?->name ?? 'N/A' }}</strong> 
                        on {{ $complaint->created_at->format('Y-m-d') }}
                    </small>
                </div>
                <div class="mt-2 mt-md-0">
                    <span class="badge 
                        @switch($complaint->status)
                            @case('received') bg-warning text-dark @break
                            @case('under_review') bg-primary @break
                            @case('resolved') bg-success @break
                            @default bg-secondary
                        @endswitch">
                        {{ ucfirst(str_replace('_',' ',$complaint->status)) }}
                    </span>
                </div>
            </div>

            <!-- Complaint Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Complaint Information</h6>
                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <p class="text-muted mb-1 fw-semibold">Complainant Name</p>
                            <p class="mb-0">{{ $complaint->user?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                        <p class="text-muted mb-1 fw-semibold">Track Id</p>
                        <p class="mb-0">{{ $complaint->track_id ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 fw-semibold">Email</p>
                            <p class="mb-0">{{ $complaint->user?->email ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 fw-semibold">Date of Incident</p>
                            <p class="mb-0">{{ $complaint->created_at->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 fw-semibold">Location</p>
                            <p class="mb-0">{{ $complaint->location ?? '-' }}</p>
                        </div>
                        <div class="col-12">
                            <p class="text-muted mb-1 fw-semibold">Description</p>
                            <p class="mb-0">{{ $complaint->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Evidence -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Attached Evidence</h6>
                    <div class="row g-3">
                        @forelse($complaint->media as $media)
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Sidebar) -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Actions</h6>
                    <form action="{{ route('admin.complaints.update', $complaint->id) }}" method="POST" class="d-flex flex-column gap-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="status" class="form-label small fw-semibold">Update Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="">Select Status</option>
                                <option value="received" {{ $complaint->status=='received' ? 'selected' : '' }}>Pending Review</option>
                                <option value="under_review" {{ $complaint->status=='under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="resolved" {{ $complaint->status=='resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                        <div>
                            <label for="officer_id" class="form-label small fw-semibold">Assign Officer</label>
                            <select id="officer_id" name="officer_id" class="form-select">
                                <option value="">Select Officer</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}" {{ $complaint->assigned_to==$officer->id ? 'selected' : '' }}>
                                        {{ $officer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="note" class="form-label small fw-semibold">Add Note</label>
                            <textarea id="note" name="note" rows="3" class="form-control" placeholder="Add any relevant notes...">@if($complaint->note){{ $complaint->note }}
    
@endif
</textarea>
                        </div>

                        <button class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2" type="submit">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Action History (Optional) -->
            {{-- <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Action History</h6>
                    @forelse($complaint->history as $h)
                        <div class="mb-3 border-bottom pb-2">
                            <p class="mb-1">{{ $h->note }}</p>
                            <small class="text-muted">by {{ $h->user?->name ?? 'Admin' }} on {{ $h->created_at->format('Y-m-d, h:i A') }}</small>
                        </div>
                    @empty
                        <p class="text-muted">No history yet.</p>
                    @endforelse
                </div>
            </div> --}}
        </div>
    </div>
</div></div>
@endsection
