@extends('layouts.master')
@push('styles')
<link href="{{ asset('css/admin/complaint-details.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="container">
<div class="ms-2 mt-4">

<div class="page-container">

    <!-- Back + heading -->
    <div class="mb-4">
        <a href="{{ route('admin.complaints.index') }}" class="back-link d-inline-flex align-items-center">
            <span class="material-symbols-outlined me-2">arrow_back</span>
            <small class="muted">Back to Complaints</small>
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="title">Complaint Details: <span class="text-muted" style="font-weight:600">#{{ $complaint->id }}</span></div>
            <div class="muted mt-1">Submitted by <strong>{{ $complaint->user?->name ?? 'N/A' }}</strong> on <span class="muted">{{ $complaint->created_at->format('Y-m-d') }}</span></div>
        </div>
        <div>
            <span class="badge-status 
                @switch($complaint->status)
                    @case('received') bg-warning text-dark @break
                    @case('under_review') bg-primary text-white @break
                    @case('resolved') bg-success text-white @break
                    @default bg-secondary text-white
                @endswitch">
                {{ ucfirst(str_replace('_',' ',$complaint->status)) }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left (Main) -->
        <div class="col-lg-8">
            <div class="card-panel mb-4">
                <h5 class="mb-3" style="font-weight:700">Complaint Information</h5>
                <div class="row g-3 text-sm">
                    <div class="col-md-6">
                        <p class="muted mb-1" style="font-weight:600; font-size:.9rem">Complainant Name</p>
                        <p class="mb-0">{{ $complaint->user?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="muted mb-1" style="font-weight:600; font-size:.9rem">Email</p>
                        <p class="mb-0">{{ $complaint->user?->email ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="muted mb-1" style="font-weight:600; font-size:.9rem">Date of Incident</p>
                        <p class="mb-0">{{ $complaint->created_at->format('Y-m-d') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="muted mb-1" style="font-weight:600; font-size:.9rem">Location</p>
                        <p class="mb-0">{{ $complaint->location ?? '-' }}</p>
                    </div>
                    <div class="col-12 mt-2">
                        <p class="muted mb-1" style="font-weight:600; font-size:.9rem">Description</p>
                        <p class="mb-0">{{ $complaint->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Evidence -->
           <!-- Attached Evidence -->
<div class="card-panel mb-4">
    <h5 class="mb-3" style="font-weight:700">Attached Evidence</h5>
    <div class="row g-3">x  
        @forelse($complaint->media as $media)
            <div class="col-6 col-md-3 evidence-item">
                <a href="{{ asset('storage/'.$media->file_path) }}" target="_blank" class="d-block text-decoration-none">
                    <div class="evidence-thumb d-flex align-items-center justify-content-center">
                        @php
                            $ext = strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION));
                        @endphp

                        @if(in_array($ext, ['jpg','jpeg','png','gif']))
                            <img src="{{ asset('storage/'.$media->file_path) }}" alt="Evidence">
                        @elseif(in_array($ext, ['mp4','mov','avi','mkv']))
                            <span class="material-symbols-outlined fs-2 text-muted">videocam</span>
                        @else
                            <span class="material-symbols-outlined fs-2 text-muted">description</span>
                        @endif
                    </div>
                    <p>{{ basename($media->file_path) }}</p>
                </a>
            </div>
        @empty
            <p class="text-muted">No evidence attached.</p>
        @endforelse
    </div>
</div>

        </div>

        <!-- Right (Sidebar) -->
        <div class="col-lg-4">
            <div class="card-panel mb-4">
                <h6 style="font-weight:700">Actions</h6>
                <form action="{{ route('admin.complaints.update', $complaint->id) }}" method="POST" class="mt-3 d-flex flex-column gap-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="status" class="form-label muted" style="font-weight:600; font-size:.9rem">Update Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Select Status</option>
                            <option value="received" {{ $complaint->status=='received' ? 'selected' : '' }}>Pending Review</option>
                            <option value="under_review" {{ $complaint->status=='under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="resolved" {{ $complaint->status=='resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <div>
                        <label for="officer_id" class="form-label muted" style="font-weight:600; font-size:.9rem">Assign Officer</label>
                        <select id="officer_id" name="officer_id" class="form-select">
                            <option value="">Select Officer</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ $complaint->assigned_to==$officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="note" class="form-label muted" style="font-weight:600; font-size:.9rem">Add Note</label>
                        <textarea id="note" name="note" rows="3" class="form-control form-textarea" placeholder="Add any relevant notes..."></textarea>
                    </div>

                    <div>
                        <button class="btn action-btn w-100 py-2 d-flex align-items-center justify-content-center gap-2 text-white" type="submit">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Action History -->
            {{-- <div class="card-panel">
                <h6 style="font-weight:700">Action History</h6>
                <div class="mt-3 d-flex flex-column gap-3">
                    @forelse($complaint->history as $h)
                    <div class="history-item">
                        <div class="history-icon">
                            <span class="material-symbols-outlined" style="color:#111827; font-size:18px;">history</span>
                        </div>
                        <div>
                            <p class="mb-1">{{ $h->note }}</p>
                            <p class="muted small mb-0">by {{ $h->user?->name ?? 'Admin' }} on {{ $h->created_at->format('Y-m-d, h:i A') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No history yet.</p>
                    @endforelse
                </div>
            </div> --}}
        </div>
    </div>

</div>
</div>
</div>
@endsection
