@extends('public_user.layouts.app')
@section('title', 'Track Complaint')
@push('styles')
  <link href="{{ asset('css/public_user/track-complaints.css') }}" rel="stylesheet">
@endpush
@section('content')
  <!-- Alerts -->
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
  <main class="container w-75 mx-auto">
    <div class="text-center mb-5">
      <h1 class="fw-bold" style="font-family: 'Eczar', serif;">Complaint Tracking</h1>
      <p class="text-muted">Track the progress of your submitted complaint using the reference number provided.</p>
    </div>

    <!-- Complaint Form -->
    <div class="card shadow-sm mb-5">
      <div class="card-body">
        <form class="row g-3 align-items-end" method="POST" action="{{ route('complaints.track.submit') }}">
          @csrf
          <div class="col-md-9">
            <label for="complaint-ref" class="form-label">Complaint Reference Number</label>
            <input type="text" name="track_id" id="complaint-ref" class="form-control form-control-lg" placeholder="Enter Complaint Reference Number" value="{{ old('ref_no') }}">
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100 btn-lg">Track</button>
          </div>
        </form>
      </div>
    </div>

    @isset($complaint)
      <!-- Complaint Details -->
      <div class="card shadow-sm mb-5">
        <div class="card-body">
          <h2 class="fw-bold" style="font-family: 'Eczar', serif;">Complaint Details</h2>
          <div class="row mt-4 g-4">
            <div class="col-md-6">
              <p class="text-muted mb-1">Tracking Number</p>
              <p class="fw-semibold">{{ $complaint->track_id }}</p>
            </div>
            <div class="col-md-6">
              <p class="text-muted mb-1">Date Submitted</p>
              <p class="fw-semibold">{{ $complaint->created_at->format('F d, Y') }}</p>
            </div>
            <div class="col-md-6">
              <p class="text-muted mb-1">Incident Type</p>
              <p class="fw-semibold">{{ $complaint->incident_type }}</p>
            </div>
            <div class="col-md-6">
              <p class="text-muted mb-1">Location</p>
              <p class="fw-semibold">{{ $complaint->location }}</p>
            </div>
            <div class="col-12">
              <p class="text-muted mb-1">Current Status</p>
              <p class="fw-bold text-success">{{ ucfirst($complaint->status) }}</p>
            </div>
            <div class="col-12">
              <p class="text-muted mb-1">Summary</p>
              <p>{{ $complaint->summary ?? 'No summary available.' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Progress Timeline -->
      {{-- <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="fw-bold" style="font-family: 'Eczar', serif;">Progress Timeline</h2>
          <ul class="timeline mt-4">
            <li class="timeline-item">
              <span class="timeline-icon"><span class="material-symbols-outlined">description</span></span>
              <p class="fw-bold mb-1">Complaint Submitted</p>
              <small class="text-muted">{{ $complaint->created_at->format('F d, Y') }}</small>
            </li>
            @if($complaint->status == 'under review' || $complaint->status == 'resolved')
              <li class="timeline-item">
                <span class="timeline-icon"><span class="material-symbols-outlined">search</span></span>
                <p class="fw-bold mb-1">Under Review</p>
                <small class="text-muted">{{ $complaint->updated_at->format('F d, Y') }}</small>
              </li>
            @endif
            @if($complaint->status == 'resolved')
              <li class="timeline-item">
                <span class="timeline-icon"><span class="material-symbols-outlined">hourglass_empty</span></span>
                <p class="fw-bold mb-1">Resolved</p>
                <small class="text-muted">{{ $complaint->updated_at->format('F d, Y') }}</small>
              </li>
            @endif
          </ul>
        </div>
      </div> --}}
      <div class="card shadow-sm">
  <div class="card-body">
    <h2 class="fw-bold" style="font-family: 'Eczar', serif;">Progress Timeline</h2>
    <ul class="timeline mt-4">

      {{-- Step 1: Complaint Submitted --}}
      <li class="timeline-item">
        <span class="timeline-icon {{ $complaint->status ? '' : 'gray' }}">
          <span class="material-symbols-outlined">description</span>
        </span>
        <p class="fw-bold mb-1">Complaint Submitted</p>
        <small class="text-muted">{{ $complaint->created_at->format('F d, Y') }}</small>
      </li>

      {{-- Step 2: Under Review --}}
      <li class="timeline-item">
        <span class="timeline-icon {{ in_array($complaint->status, ['under_review','resolved']) ? '' : 'gray' }}">
          <span class="material-symbols-outlined">search</span>
        </span>
        @if ($complaint->status == 'under_review')
          <p class="fw-bold mb-1">Under Review</p>
          <small class="text-muted">{{ $complaint->updated_at->format('F d, Y') }}</small>
        @elseif ($complaint->status == 'resolved')
          <p class="fw-bold mb-1">Under Review</p>
          <small class="text-muted">Completed earlier</small>
        @else
          <p class="text-muted mb-1">Under Review</p>
          <small class="text-muted">Pending Review</small>
        @endif
      </li>

      {{-- Step 3: Investigation Assigned --}}
      <li class="timeline-item">
        <span class="timeline-icon {{ $complaint->assigned_to ? '' : 'gray' }}">
          <span class="material-symbols-outlined">person</span>
        </span>
        @if ($complaint->assigned_to)
          <p class="fw-bold mb-1">Investigation Assigned</p>
          <small class="text-muted">Assigned to {{ $complaint->user->name }}</small>
        @else
          <p class="text-muted mb-1">Investigation Assigned</p>
          <small class="text-muted">Not Assigned Yet (Upcoming)</small>
        @endif
      </li>

      {{-- Step 4: Final Status --}}
      <li class="timeline-item">
        <span class="timeline-icon {{ $complaint->status == 'resolved' ? '' : 'gray' }}">
          <span class="material-symbols-outlined">hourglass_empty</span>
        </span>
        @if ($complaint->status == 'resolved')
          <p class="fw-bold mb-1">Status: Resolved</p>
          <small class="text-muted">Resolved at {{ $complaint->updated_at->format('F d, Y') }}</small>
        @elseif ($complaint->status == 'under_review')
          <p class="fw-bold mb-1">Status: Under Review</p>
          <small class="text-muted">{{ $complaint->updated_at->format('F d, Y') }}</small>
        @else
          <p class="text-muted mb-1">Status</p>
          <small class="text-muted">Not Resolved Yet</small>
        @endif
      </li>

    </ul>
  </div>
</div>

    @endisset
  </main>

 @endsection
