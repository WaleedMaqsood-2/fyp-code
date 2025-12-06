@extends('forensic_analyst.layouts.app')
@section('title', 'AI Transcript Verification')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-primary mb-4">AI Transcript Verification</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3 text-primary">
                <span class="material-icons align-middle">description</span> Evidence Transcripts
            </h5>

            <!-- Search and Filter Section -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="material-icons">search</i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search by Case ID, Complaint Title, or Transcript content..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <select class="form-select" id="filterMediaType">
                            <option value="">All Media Types</option>
                            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                        </select>
                        <select class="form-select" id="filterStatus">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        </select>
                        <button class="btn btn-outline-secondary" id="resetFilters">
                            <i class="material-icons">refresh</i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Tags -->
            @if(request()->hasAny(['search', 'type', 'status']))
            <div class="mb-3">
                <small class="text-muted">Active Filters:</small>
                @if(request('search'))
                <span class="badge bg-info me-2">
                    Search: "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithoutQuery('search') }}" class="text-white ms-1">
                        <i class="material-icons" style="font-size: 14px;">close</i>
                    </a>
                </span>
                @endif
                @if(request('type'))
                <span class="badge bg-info me-2">
                    Type: {{ ucfirst(request('type')) }}
                    <a href="{{ request()->fullUrlWithoutQuery('type') }}" class="text-white ms-1">
                        <i class="material-icons" style="font-size: 14px;">close</i>
                    </a>
                </span>
                @endif
                @if(request('status'))
                <span class="badge bg-info me-2">
                    Status: {{ ucfirst(request('status')) }}
                    <a href="{{ request()->fullUrlWithoutQuery('status') }}" class="text-white ms-1">
                        <i class="material-icons" style="font-size: 14px;">close</i>
                    </a>
                </span>
                @endif
                <a href="{{ route('forensic.transcript') }}" class="btn btn-sm btn-outline-danger">
                    Clear All Filters
                </a>
            </div>
            @endif

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons text-primary">description</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Total Transcripts</h6>
                                    <p class="mb-0 fw-bold">{{ $evidences->total() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons text-success">videocam</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Video</h6>
                                    <p class="mb-0 fw-bold">{{ $videoCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons text-warning">audiotrack</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Audio</h6>
                                    <p class="mb-0 fw-bold">{{ $audioCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons {{ $pendingCount > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $pendingCount > 0 ? 'pending_actions' : 'check_circle' }}
                                    </i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Pending</h6>
                                    <p class="mb-0 fw-bold {{ $pendingCount > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $pendingCount }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transcript Status Tabs -->
            <ul class="nav nav-tabs mb-3" id="transcriptTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                        All Transcripts ({{ $evidences->total() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        Pending ({{ $pendingCount }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="verified-tab" data-bs-toggle="tab" data-bs-target="#verified" type="button" role="tab">
                        Verified ({{ $verifiedCount }})
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="transcriptTabContent">
                <!-- All Transcripts Tab -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    @if($evidences->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th width="150">Case Details</th>
                                    <th width="80">Type</th>
                                    <th width="120">AI Transcript</th>
                                    <th>Your Correction</th>
                                    <th width="100">Status</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evidences as $m)
                                @php
                                    $aiTranscript = optional($m->transcription)->transcript;
                                    $userVerification = $m->transcriptionVerifications->first();
                                    $hasVerification = $userVerification ? true : false;
                                    $isApproved = $hasVerification ? $userVerification->approved : false;
                                @endphp
                                
                                @if($aiTranscript)
                                <tr>
                                    <td>{{ $m->id }}</td>
                                    <td>
                                        <div class="fw-bold small">{{ $m->complaint->subject ?? 'N/A' }}</div>
                                        <small class="text-muted d-block">Track: {{ $m->track_id ?? 'N/A' }}</small>
                                        <small class="text-muted">File: {{ \Illuminate\Support\Str::limit(basename($m->file_path), 15) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $m->file_type == 'video' ? 'bg-success' : 'bg-warning' }}">
                                            <i class="material-icons align-middle" style="font-size: 14px;">
                                                {{ $m->file_type == 'video' ? 'videocam' : 'audiotrack' }}
                                            </i>
                                            {{ ucfirst($m->file_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="ai-transcript-preview">
                                            <div class="card bg-light border-0">
                                                <div class="card-body py-2 px-3">
                                                    <small class="text-muted d-block mb-1">AI Generated:</small>
                                                    <p class="mb-0 small">
                                                        {{ \Illuminate\Support\Str::limit($aiTranscript, 80) }}
                                                        @if(strlen($aiTranscript) > 80)
                                                        <a href="#" class="text-primary" 
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#viewAiTranscriptModal{{ $m->id }}">
                                                            Read full
                                                        </a>
                                                        @endif
                                                    </p>
                                                    <small class="text-muted">
                                                        {{ str_word_count($aiTranscript) }} words
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($hasVerification)
                                        <div class="verified-transcript-preview">
                                            <div class="card {{ $isApproved ? 'border-success' : 'border-warning' }}">
                                                <div class="card-body py-2 px-3">
                                                    <small class="text-muted d-block mb-1">
                                                        Your Correction:
                                                        @if($isApproved)
                                                        <span class="badge bg-success ms-1">Approved</span>
                                                        @else
                                                        <span class="badge bg-warning ms-1">Draft</span>
                                                        @endif
                                                    </small>
                                                    <p class="mb-0 small">
                                                        {{ \Illuminate\Support\Str::limit($userVerification->corrected_text, 80) }}
                                                    </p>
                                                    <small class="text-muted">
                                                        {{ str_word_count($userVerification->corrected_text) }} words
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="text-center text-muted py-2">
                                            <i class="material-icons">edit_note</i>
                                            <div class="small mt-1">Not verified yet</div>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hasVerification)
                                            @if($isApproved)
                                            <span class="badge bg-success">
                                                <i class="material-icons align-middle" style="font-size: 14px;">verified</i> Approved
                                            </span>
                                            @else
                                            <span class="badge bg-warning">
                                                <i class="material-icons align-middle" style="font-size: 14px;">edit</i> Draft
                                            </span>
                                            @endif
                                        @else
                                        <span class="badge bg-secondary">
                                            <i class="material-icons align-middle" style="font-size: 14px;">pending</i> Pending
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm {{ $hasVerification ? 'btn-outline-primary' : 'btn-primary' }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verifyTranscriptModal{{ $m->id }}">
                                            <i class="material-icons align-middle" style="font-size: 16px;">
                                                {{ $hasVerification ? 'edit' : 'playlist_add_check' }}
                                            </i>
                                            {{ $hasVerification ? 'Edit' : 'Verify' }}
                                        </button>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="material-icons" style="font-size: 64px;">description</i>
                            <h4 class="mt-3">No Transcripts Found</h4>
                            @if(request()->hasAny(['search', 'type', 'status']))
                            <p>Try changing your search or filter criteria</p>
                            @else
                            <p>No AI generated transcripts available for verification</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Pending Tab -->
                <div class="tab-pane fade" id="pending" role="tabpanel">
                    @php
                        $pendingEvidences = $evidences->filter(function($m) {
                            $userVerification = $m->transcriptionVerifications->first();
                            return optional($m->transcription)->transcript && 
                                   (!$userVerification || !$userVerification->approved);
                        });
                    @endphp
                    
                    @if($pendingEvidences->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Case Title</th>
                                    <th>Type</th>
                                    <th>AI Transcript Preview</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingEvidences as $m)
                                <tr>
                                    <td>{{ $m->id }}</td>
                                    <td>{{ $m->complaint->subject ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $m->file_type == 'video' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($m->file_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            {{ \Illuminate\Support\Str::limit($m->transcription->transcript, 100) }}
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verifyTranscriptModal{{ $m->id }}">
                                            Verify Now
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="material-icons" style="font-size: 64px;">check_circle</i>
                            <h4 class="mt-3">All Caught Up!</h4>
                            <p>No pending transcripts for verification</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Verified Tab -->
                <div class="tab-pane fade" id="verified" role="tabpanel">
                    @php
                        $verifiedEvidences = $evidences->filter(function($m) {
                            $userVerification = $m->transcriptionVerifications->first();
                            return $userVerification && $userVerification->approved;
                        });
                    @endphp
                    
                    @if($verifiedEvidences->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Case Title</th>
                                    <th>Type</th>
                                    <th>Verified Transcript</th>
                                    <th>Verified On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($verifiedEvidences as $m)
                                @php
                                    $userVerification = $m->transcriptionVerifications->first();
                                @endphp
                                <tr>
                                    <td>{{ $m->id }}</td>
                                    <td>{{ $m->complaint->subject ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $m->file_type == 'video' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($m->file_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            {{ \Illuminate\Support\Str::limit($userVerification->corrected_text, 120) }}
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $userVerification->updated_at->format('M d, Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verifyTranscriptModal{{ $m->id }}">
                                            View/Edit
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="material-icons" style="font-size: 64px;">pending</i>
                            <h4 class="mt-3">No Verified Transcripts</h4>
                            <p>You haven't approved any transcripts yet</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pagination -->
            @if($evidences->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Showing {{ $evidences->firstItem() }} to {{ $evidences->lastItem() }} of {{ $evidences->total() }} transcripts
                    </small>
                </div>
                <div>
                    {{ $evidences->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
@foreach($evidences as $m)
@if(optional($m->transcription)->transcript)
@php
    $aiTranscript = optional($m->transcription)->transcript;
    $userVerification = $m->transcriptionVerifications->first();
    $hasVerification = $userVerification ? true : false;
    $correctedText = $hasVerification ? $userVerification->corrected_text : $aiTranscript;
    $isApproved = $hasVerification ? $userVerification->approved : false;
@endphp

<!-- AI Transcript View Modal -->
<div class="modal fade" id="viewAiTranscriptModal{{ $m->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">AI Generated Transcript - Evidence #{{ $m->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Original AI Transcript</h6>
                    </div>
                    <div class="card-body">
                        <div class="transcript-content" style="max-height: 400px; overflow-y: auto;">
                            <p style="white-space: pre-wrap;">{{ $aiTranscript }}</p>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="material-icons align-middle" style="font-size: 14px;">info</i>
                                {{ str_word_count($aiTranscript) }} words | 
                                AI Generated
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" data-bs-toggle="modal" 
                        data-bs-target="#verifyTranscriptModal{{ $m->id }}" 
                        data-bs-dismiss="modal">
                    <i class="material-icons align-middle">edit</i> Verify & Correct
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verifyTranscriptModal{{ $m->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify & Correct Transcript - Evidence #{{ $m->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('forensic.transcript.update') }}" method="POST">
                @csrf
                <input type="hidden" name="media_id" value="{{ $m->id }}">
                
                <div class="modal-body">
                    <div class="row">
                        <!-- AI Transcript Column -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="material-icons align-middle">smart_toy</i> AI Generated Transcript
                                        <small class="text-muted float-end">{{ str_word_count($aiTranscript) }} words</small>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="ai-transcript-view" style="height: 400px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px;">
                                        <p style="white-space: pre-wrap; line-height: 1.6;">{{ $aiTranscript }}</p>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="material-icons align-middle" style="font-size: 14px;">info</i>
                                            This is the original transcript generated by AI. Please verify and correct any errors.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Correction Column -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="material-icons align-middle">edit_note</i> Your Corrected Version
                                        @if($hasVerification)
                                        <span class="badge {{ $isApproved ? 'bg-success' : 'bg-warning' }} float-end">
                                            {{ $isApproved ? 'Approved' : 'Draft' }}
                                        </span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Corrected Transcript</label>
                                        <textarea name="corrected_text" rows="15" class="form-control" 
                                                  placeholder="Edit the transcript here. Correct any errors, add punctuation, or improve clarity."
                                                  style="font-family: 'Courier New', monospace; font-size: 14px;">{{ $correctedText }}</textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Verification Status</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="approved" id="draft{{ $m->id }}" value="0" {{ !$isApproved ? 'checked' : '' }}>
                                            <label class="form-check-label" for="draft{{ $m->id }}">
                                                <span class="badge bg-warning">Save as Draft</span>
                                                <small class="text-muted"> - Will appear in pending verification</small>
                                            </label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="approved" id="approve{{ $m->id }}" value="1" {{ $isApproved ? 'checked' : '' }}>
                                            <label class="form-check-label" for="approve{{ $m->id }}">
                                                <span class="badge bg-success">Mark as Verified/Approved</span>
                                                <small class="text-muted"> - Transcript will be marked as verified</small>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    @if($hasVerification)
                                    <div class="alert alert-info">
                                        <small>
                                            <i class="material-icons align-middle" style="font-size: 14px;">info</i>
                                            Last saved: {{ $userVerification->updated_at->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="material-icons align-middle">save</i> Save Verification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<style>
    .ai-transcript-preview, .verified-transcript-preview {
        max-height: 120px;
        overflow: hidden;
    }
    
    .transcript-content {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }
    
    .nav-tabs .nav-link {
        border-radius: 0.375rem 0.375rem 0 0;
    }
    
    .nav-tabs .nav-link.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    
    .ai-transcript-view {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
    }
</style>

<script>
// Filter functionality
document.getElementById('filterMediaType').addEventListener('change', function() {
    const type = this.value;
    const url = new URL(window.location.href);
    
    if (type) {
        url.searchParams.set('type', type);
    } else {
        url.searchParams.delete('type');
    }
    
    window.location.href = url.toString();
});

document.getElementById('filterStatus').addEventListener('change', function() {
    const status = this.value;
    const url = new URL(window.location.href);
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    window.location.href = url.toString();
});

// Reset filters
document.getElementById('resetFilters').addEventListener('click', function() {
    window.location.href = '{{ route("forensic.transcript") }}';
});

// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#transcriptTabs .nav-link');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-bs-target').replace('#', '');
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', url);
        });
    });
    
    // Activate tab from URL
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab) {
        const tabElement = document.querySelector(`[data-bs-target="#${activeTab}"]`);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }
});
</script>
@endsection