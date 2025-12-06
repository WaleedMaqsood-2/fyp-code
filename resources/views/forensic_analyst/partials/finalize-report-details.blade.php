{{-- resources/views/forensic_analyst/report-detail.blade.php --}}
@extends('forensic_analyst.layouts.app')
@section('title', 'Report Details')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <i class="material-icons align-middle">description</i> Report Details
        </h2>
        <div>
            <a href="{{ route('forensic.finalize.generated') }}" class="btn btn-outline-secondary">
                <i class="material-icons align-middle">arrow_back</i> Back to Reports
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Report Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Report ID:</th>
                            <td>{{ $report->id }}</td>
                        </tr>
                        <tr>
                            <th>File Name:</th>
                            <td>
                                <i class="material-icons align-middle text-primary">picture_as_pdf</i>
                                {{ basename($report->file_path) }}
                            </td>
                        </tr>
                        <tr>
                            <th>File Size:</th>
                            <td>{{ $report->fileSize }}</td>
                        </tr>
                        <tr>
                            <th>Generated On:</th>
                            <td>
                                {{ $report->exported_at->format('F j, Y H:i:s') }}
                                <br>
                                <small class="text-muted">{{ $report->exported_at->diffForHumans() }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>File Status:</th>
                            <td>
                                @if($report->fileExists())
                                    <span class="badge bg-success">Available</span>
                                @else
                                    <span class="badge bg-danger">File Missing</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        @if($report->complaint)
                        <tr>
                            <th width="40%">Case ID:</th>
                            <td>
                                <strong>{{ $report->complaint->track_id ?? 'N/A' }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Case Title:</th>
                            <td>{{ $report->complaint->subject ?? 'Untitled' }}</td>
                        </tr>
                        <tr>
                            <th>Case Created:</th>
                            <td>{{ $report->complaint->created_at->format('M d, Y') ?? 'N/A' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Storage Path:</th>
                            <td>
                                <code class="small">storage/app/public/{{ $report->file_path }}</code>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- File Actions -->
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">File Actions</h6>
                        <small class="text-muted">Manage the generated report file</small>
                    </div>
                    <div class="btn-group" role="group">
                        @if($report->fileExists())
                        <a href="{{ route('forensic.finalize.download', $report->id) }}" 
                           class="btn btn-success">
                            <i class="material-icons align-middle">download</i> Download PDF
                        </a>
                        <button type="button" class="btn btn-info" onclick="previewPDF()">
                            <i class="material-icons align-middle">visibility</i> Preview
                        </button>
                        @endif
                   {{-- Delete button --}}
<form action="{{ route('forensic.finalize.delete', $report->id) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this report? This action cannot be undone.')">
        <i class="material-icons align-middle">delete</i> Delete Report
    </button>
</form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($report->complaint)
    <!-- Case Details -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="material-icons align-middle">folder</i> Case Details
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Case Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Track ID:</th>
                            <td>{{ $report->complaint->track_id }}</td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>{{ $report->complaint->subject }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $report->complaint->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Incident Type:</th>
                            <td>{{ $report->complaint->incident_type ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Location:</th>
                            <td>{{ $report->complaint->location ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Case Status</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Status:</th>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'completed' => 'success',
                                        'closed' => 'secondary'
                                    ];
                                    $color = $statusColors[$report->complaint->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $report->complaint->status)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $report->complaint->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $report->complaint->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Assigned To:</th>
                            <td>{{ $report->complaint->assignedUser->name ?? 'Not Assigned' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Case Summary -->
    @if($report->complaint->summaries && $report->complaint->summaries->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="material-icons align-middle">summarize</i> Approved Summary
            </h5>
        </div>
        <div class="card-body">
            @foreach($report->complaint->summaries as $summary)
                @if($summary->status === 'approved')
                <div class="summary-content p-3 bg-light rounded">
                    <p style="white-space: pre-wrap;">{{ $summary->summary_text }}</p>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="material-icons align-middle" style="font-size: 14px;">person</i>
                            Generated by: {{ $summary->user->name ?? 'System' }}
                        </small>
                        <small class="text-muted">
                            <i class="material-icons align-middle" style="font-size: 14px;">schedule</i>
                            {{ $summary->updated_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Evidence Files -->
    @if($report->complaint->media && $report->complaint->media->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="material-icons align-middle">attachment</i> Evidence Files
                <span class="badge bg-dark ms-2">{{ $report->complaint->media->count() }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($report->complaint->media as $media)
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center p-3">
                            @if($media->file_type === 'image')
                                <div class="mb-2">
                                    <i class="material-icons text-primary" style="font-size: 48px;">image</i>
                                </div>
                                <h6 class="text-truncate mb-1" title="{{ basename($media->file_path) }}">
                                    {{ \Illuminate\Support\Str::limit(basename($media->file_path), 20) }}
                                </h6>
                                <small class="text-muted">Image File</small>
                            @elseif($media->file_type === 'video')
                                <div class="mb-2">
                                    <i class="material-icons text-danger" style="font-size: 48px;">videocam</i>
                                </div>
                                <h6 class="text-truncate mb-1" title="{{ basename($media->file_path) }}">
                                    {{ \Illuminate\Support\Str::limit(basename($media->file_path), 20) }}
                                </h6>
                                <small class="text-muted">Video File</small>
                            @elseif($media->file_type === 'audio')
                                <div class="mb-2">
                                    <i class="material-icons text-success" style="font-size: 48px;">audiotrack</i>
                                </div>
                                <h6 class="text-truncate mb-1" title="{{ basename($media->file_path) }}">
                                    {{ \Illuminate\Support\Str::limit(basename($media->file_path), 20) }}
                                </h6>
                                <small class="text-muted">Audio File</small>
                            @else
                                <div class="mb-2">
                                    <i class="material-icons text-secondary" style="font-size: 48px;">insert_drive_file</i>
                                </div>
                                <h6 class="text-truncate mb-1" title="{{ basename($media->file_path) }}">
                                    {{ \Illuminate\Support\Str::limit(basename($media->file_path), 20) }}
                                </h6>
                                <small class="text-muted">Document</small>
                            @endif
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $media->file_path) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="material-icons align-middle" style="font-size: 14px;">visibility</i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @else
    <!-- No Case Information -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="material-icons align-middle">warning</i> Case Information Not Found
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="material-icons align-middle">info</i>
                The associated case information could not be found. The case may have been deleted or the relationship is not properly configured.
            </div>
            <p>Report was generated for review ID: <strong>{{ $report->review_id }}</strong></p>
            <p>Please check the database relationships between report_exports and complaints tables.</p>
        </div>
    </div>
    @endif

   <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PDF Preview - {{ basename($report->file_path) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    @if($report->fileExists())
                    <div class="alert alert-info">
                        <i class="material-icons align-middle">info</i>
                        PDF preview requires download. Click the download button to view the complete report.
                    </div>
                    
                    <div class="pdf-placeholder bg-light rounded p-5 mb-3">
                        <i class="material-icons" style="font-size: 64px; color: #dc3545;">picture_as_pdf</i>
                        <h5 class="mt-3">{{ basename($report->file_path) }}</h5>
                        <p class="text-muted">File Size: {{ $report->fileSize }}</p>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('forensic.finalize.download', $report->id) }}" 
                           class="btn btn-primary me-md-2">
                            <i class="material-icons align-middle">download</i> Download PDF
                        </a>
                        <a href="{{ asset('storage/' . $report->file_path) }}" 
                           target="_blank" 
                           class="btn btn-outline-primary">
                            <i class="material-icons align-middle">open_in_new</i> Open in New Tab
                        </a>
                    </div>
                    @else
                    <div class="alert alert-danger">
                        <i class="material-icons align-middle">error</i>
                        PDF file not found for preview.
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    .summary-content {
        max-height: 300px;
        overflow-y: auto;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 4px solid #198754;
    }
    
    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }
    
    table.table-sm th {
        font-weight: 600;
        color: #495057;
    }
</style>

<script>
function previewPDF() {
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

function confirmDelete(reportId) {
    if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        // POST request send کریں
        fetch('{{ url("forensic/finalize/delete") }}/' + reportId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({}) // _method نہیں بھیجنا
        })
        .then(response => {
            if (response.ok) {
                location.reload(); // Page refresh
            } else {
                alert('Error deleting report');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting report');
        });
    }
}

// Auto-refresh file status
document.addEventListener('DOMContentLoaded', function() {
    // You can add auto-refresh logic here if needed
    console.log('Report detail page loaded');
    
    // Add file status indicator
    const fileStatus = document.querySelector('.file-status');
    if (fileStatus) {
        // Check file status periodically (every 30 seconds)
        setInterval(() => {
            fetch('{{ route("forensic.finalize.check-file", $report->id) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        fileStatus.innerHTML = '<span class="badge bg-success">Available</span>';
                    } else {
                        fileStatus.innerHTML = '<span class="badge bg-danger">File Missing</span>';
                    }
                });
        }, 30000);
    }
});
</script>
@endsection