@extends('forensic_analyst.layouts.app')
@section('title', 'Finalize Forensic Report')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-primary mb-4">
        <span class="material-icons align-middle">description</span> Finalize Forensic Report
    </h2>

  
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        @if(session('download_url'))
        <div class="mt-2">
            <a href="{{ session('download_url') }}" class="btn btn-sm btn-success">
                <i class="material-icons align-middle">download</i> Download Now
            </a>
        </div>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
    <div class="card mb-4">
        <div class="card-body">
            <!-- Search and Filter Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <form method="GET" action="" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="material-icons">search</i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search by Case ID or Title..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <select class="form-select" id="filterStatus">
                            <option value="">All Cases</option>
                            <option value="not_exported" {{ request('status') == 'not_exported' ? 'selected' : '' }}>Pending Export</option>
                            <option value="exported" {{ request('status') == 'exported' ? 'selected' : '' }}>Already Exported</option>
                        </select>
                        <a href="{{ route('forensic.finalize.generated') }}" class="btn btn-success">
                            <i class="material-icons align-middle">history</i> Generated Reports
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Tags -->
            @if(request()->hasAny(['search', 'status']))
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
                @if(request('status'))
                <span class="badge bg-info me-2">
                    Status: {{ request('status') == 'exported' ? 'Exported' : 'Pending Export' }}
                    <a href="{{ request()->fullUrlWithoutQuery('status') }}" class="text-white ms-1">
                        <i class="material-icons" style="font-size: 14px;">close</i>
                    </a>
                </span>
                @endif
                <a href="{{ route('forensic.finalize') }}" class="btn btn-sm btn-outline-danger">
                    Clear All Filters
                </a>
            </div>
            @endif

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons text-primary">folder</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Ready Cases</h6>
                                    <p class="mb-0 fw-bold">{{ $stats['total'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons text-warning">pending</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Pending Export</h6>
                                    <p class="mb-0 fw-bold">{{ $stats['pending_export'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons text-success">picture_as_pdf</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Exported</h6>
                                    <p class="mb-0 fw-bold">{{ $stats['exported'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cases List -->
            @if($cases->count() > 0)
                @foreach($cases as $case)
                @php
                    // Get latest approved summary
                    $approvedSummary = $case->summaries->first();
                    
                    // Get latest verified transcript
                    $verifiedTranscript = null;
                    foreach ($case->transcriptions as $transcription) {
                        if ($transcription->verifications->isNotEmpty()) {
                            $verifiedTranscript = $transcription->verifications->first();
                            break;
                        }
                    }
                    
                    // Check if already exported
                    $isExported = $case->reportExports->isNotEmpty();
                    $latestExport = $isExported ? $case->reportExports->first() : null;
                @endphp
                
                <div class="card mb-4 {{ $isExported ? 'border-success' : 'border-warning' }}">
                    <div class="card-header {{ $isExported ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 {{ $isExported ? 'text-success' : 'text-warning' }}">
                                <span class="material-icons align-middle">
                                    {{ $isExported ? 'check_circle' : 'pending' }}
                                </span>
                                Case #{{ $case->track_id }} — {{ $case->subject }}
                            </h5>
                            <div>
                                @if($isExported)
                                <span class="badge bg-success">
                                    <i class="material-icons align-middle">check</i> Exported
                                </span>
                                <small class="text-muted ms-2">
                                    {{ $latestExport->exported_at->format('M d, Y H:i') }}
                                </small>
                                @else
                                <span class="badge bg-warning">
                                    <i class="material-icons align-middle">pending</i> Ready for Export
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Case Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Case Details</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="120">Case ID:</th>
                                        <td>{{ $case->track_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Title:</th>
                                        <td>{{ $case->subject }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>{{ $case->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if($isExported)
                                            <span class="badge bg-success">Report Generated</span>
                                            @else
                                            <span class="badge bg-warning">Ready for Finalization</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Content Status</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="140">Transcript:</th>
                                        <td>
                                            @if($verifiedTranscript)
                                            <span class="badge bg-success">Verified</span>
                                            @else
                                            <span class="badge bg-secondary">Not Available</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Summary:</th>
                                        <td>
                                            @if($approvedSummary)
                                            <span class="badge bg-success">Approved</span>
                                            @else
                                            <span class="badge bg-secondary">Not Available</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Evidence:</th>
                                        <td>
                                            <span class="badge bg-info">{{ $case->media->count() }} files</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Validated Transcript -->
                        @if($verifiedTranscript)
                        <div class="mb-4">
                            <h6 class="text-primary">
                                <i class="material-icons align-middle">verified</i> Validated Transcript
                                <small class="text-muted float-end">
                                    Verified by: {{ $verifiedTranscript->analyst->name ?? 'Analyst' }}
                                </small>
                            </h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="transcript-content" style="max-height: 200px; overflow-y: auto;">
                                        <p style="white-space: pre-wrap;">{{ $verifiedTranscript->corrected_text ?? $verifiedTranscript->transcript }}</p>
                                    </div>
                                    <small class="text-muted">
                                        <i class="material-icons align-middle" style="font-size: 14px;">info</i>
                                        Verified on: {{ $verifiedTranscript->updated_at->format('M d, Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Approved Summary -->
                        @if($approvedSummary)
                        <div class="mb-4">
                            <h6 class="text-success">
                                <i class="material-icons align-middle">summarize</i> Approved Summary
                                <small class="text-muted float-end">
                                    Approved by: {{ $approvedSummary->approvedBy->name ?? 'System' }}
                                </small>
                            </h6>
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="summary-content" style="max-height: 150px; overflow-y: auto;">
                                        <p style="white-space: pre-wrap;">{{ $approvedSummary->summary_text }}</p>
                                    </div>
                                    <small class="text-muted">
                                        <i class="material-icons align-middle" style="font-size: 14px;">info</i>
                                        Approved on: {{ $approvedSummary->updated_at->format('M d, Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Attached Evidence -->
                        @if($case->media->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-info">
                                <i class="material-icons align-middle">attachment</i> Attached Evidence
                                <span class="badge bg-info">{{ $case->media->count() }} files</span>
                            </h6>
                            <div class="row">
                                @foreach($case->media as $media)
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card evidence-card h-100">
                                        <div class="card-body text-center p-2">
                                            @if($media->file_type === 'image')
                                                <img src="{{ asset('storage/' . $media->file_path) }}" 
                                                     alt="Evidence" 
                                                     class="img-fluid rounded" 
                                                     style="height: 120px; object-fit: cover;">
                                                <p class="mt-2 small text-muted mb-1">
                                                    <i class="material-icons align-middle" style="font-size: 14px;">image</i> Image
                                                </p>
                                            @elseif($media->file_type === 'video')
                                                <div class="video-thumbnail bg-dark rounded d-flex align-items-center justify-content-center" 
                                                     style="height: 120px;">
                                                    <i class="material-icons text-white" style="font-size: 48px;">play_circle</i>
                                                </div>
                                                <p class="mt-2 small text-muted mb-1">
                                                    <i class="material-icons align-middle" style="font-size: 14px;">videocam</i> Video
                                                </p>
                                            @elseif($media->file_type === 'audio')
                                                <div class="audio-thumbnail bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="height: 120px;">
                                                    <i class="material-icons text-primary" style="font-size: 48px;">audiotrack</i>
                                                </div>
                                                <p class="mt-2 small text-muted mb-1">
                                                    <i class="material-icons align-middle" style="font-size: 14px;">audiotrack</i> Audio
                                                </p>
                                            @else
                                                <div class="file-thumbnail bg-secondary rounded d-flex align-items-center justify-content-center" 
                                                     style="height: 120px;">
                                                    <i class="material-icons text-white" style="font-size: 48px;">insert_drive_file</i>
                                                </div>
                                                <p class="mt-2 small text-muted mb-1">
                                                    <i class="material-icons align-middle" style="font-size: 14px;">description</i> Document
                                                </p>
                                            @endif
                                            <small class="text-truncate d-block" title="{{ basename($media->file_path) }}">
                                                {{ \Illuminate\Support\Str::limit(basename($media->file_path), 20) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                @if($isExported)
                                <small class="text-muted">
                                    <i class="material-icons align-middle" style="font-size: 14px;">info</i>
                                    Report already generated. You can download or regenerate.
                                </small>
                                @else
                                <small class="text-muted">
                                    <i class="material-icons align-middle" style="font-size: 14px;">info</i>
                                    This case is ready for final report generation.
                                </small>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                @if($isExported)
                                <a href="{{ route('forensic.finalize.download', $latestExport->id) }}" 
                                   class="btn btn-success">
                                    <i class="material-icons align-middle">download</i> Download PDF
                                </a>
                                @endif
                                <a href="{{ route('forensic.finalize.export', $case->id) }}" 
                                   class="btn btn-primary">
                                    <i class="material-icons align-middle">picture_as_pdf</i> 
                                    {{ $isExported ? 'Regenerate PDF' : 'Generate PDF Report' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                @if($cases->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <small class="text-muted">
                            Showing {{ $cases->firstItem() }} to {{ $cases->lastItem() }} of {{ $cases->total() }} cases
                        </small>
                    </div>
                    <div>
                        {{ $cases->links() }}
                    </div>
                </div>
                @endif
            @else
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="material-icons" style="font-size: 64px;">folder_open</i>
                    <h4 class="mt-3">No Cases Ready</h4>
                    <p>No cases are ready for final report generation.</p>
                    <p class="small">Make sure cases have approved summaries and verified transcripts.</p>
                    @if(request()->hasAny(['search', 'status']))
                    <p class="mt-3">
                        <a href="{{ route('forensic.finalize') }}" class="btn btn-outline-primary">
                            Clear Filters
                        </a>
                    </p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .evidence-card {
        transition: transform 0.2s;
        border: 1px solid #dee2e6;
    }
    
    .evidence-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .video-thumbnail, .audio-thumbnail, .file-thumbnail {
        transition: all 0.3s;
    }
    
    .video-thumbnail:hover {
        background-color: #343a40 !important;
    }
    
    .audio-thumbnail:hover {
        background-color: #e9ecef !important;
    }
</style>

<script>
// Filter functionality
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

// Auto-expand textareas on click
document.querySelectorAll('.transcript-content, .summary-content').forEach(element => {
    element.addEventListener('click', function() {
        if (this.style.maxHeight === 'none') {
            this.style.maxHeight = '200px';
        } else {
            this.style.maxHeight = 'none';
        }
    });
});
</script>
@endsection