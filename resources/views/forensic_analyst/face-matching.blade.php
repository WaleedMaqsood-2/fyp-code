@extends('forensic_analyst.layouts.app')
@section('title', 'Face Matching - Forensic Analyst')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4">
        <i class="material-icons align-middle">face</i> Face Matching System
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Statistics -->
        <div class="col-12 mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons text-primary">fingerprint</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Total Matches</h6>
                                    <p class="mb-0 fw-bold">{{ $stats['total'] }}</p>
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
                                    <i class="material-icons text-success">verified</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Verified</h6>
                                    <p class="mb-0 fw-bold">{{ $stats['verified'] }}</p>
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
                                    <i class="material-icons text-warning">pending</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Pending</h6>
                                    <p class="mb-0 fw-bold">{{ $stats['pending'] }}</p>
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
                                    <i class="material-icons text-danger">trending_up</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">High Confidence</h6>
                                    <p class="mb-0 fw-bold">{{ $stats['high_confidence'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Panel -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="material-icons align-middle">upload</i> Upload Reference Face
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('forensic.face.match.post') }}" method="POST" enctype="multipart/form-data" id="faceUploadForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Reference Face Image</label>
                            <div class="input-group">
                                <input type="file" name="face_image" class="form-control" 
                                       accept="image/*" required id="faceImageInput">
                                <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('faceImageInput').click()">
                                    <i class="material-icons align-middle">folder_open</i>
                                </button>
                            </div>
                            <small class="text-muted">Upload a clear face image for matching (Max: 5MB)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Associated Case (Optional)</label>
                            <select name="complaint_id" class="form-select">
                                <option value="">Select Case</option>
                                @foreach($casesWithImages as $case)
                                    <option value="{{ $case->id }}">
                                        {{ $case->track_id }} - {{ $case->subject }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Link this search to a specific case</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" 
                                      placeholder="Add any notes about this face search..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" id="uploadBtn">
                                <i class="material-icons align-middle">search</i> 
                                <span id="btnText">Find Matches</span>
                                <span class="spinner-border spinner-border-sm d-none" id="btnSpinner"></span>
                            </button>
                        </div>
                    </form>

                    <!-- Preview -->
                    <div class="mt-3 text-center">
                        <div id="imagePreview" class="d-none">
                            <h6>Image Preview:</h6>
                            <img id="previewImage" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="material-icons align-middle">bolt</i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('forensic.face.match') }}" class="btn btn-outline-primary">
                            <i class="material-icons align-middle">refresh</i> Refresh Matches
                        </a>
                        <button class="btn btn-outline-warning" onclick="clearFilters()">
                            <i class="material-icons align-middle">clear_all</i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matches Panel -->
        <div class="col-lg-8 mb-4">
            <!-- Search and Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <form method="GET" action="">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="material-icons">search</i>
                                    </span>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search by Case ID or Title..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex gap-2">
                                <select class="form-select" id="statusFilter" onchange="applyFilter()">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                <select class="form-select" id="confidenceFilter" onchange="applyFilter()">
                                    <option value="">Min Confidence</option>
                                    <option value="80" {{ request('min_confidence') == '80' ? 'selected' : '' }}>80%+</option>
                                    <option value="70" {{ request('min_confidence') == '70' ? 'selected' : '' }}>70%+</option>
                                    <option value="60" {{ request('min_confidence') == '60' ? 'selected' : '' }}>60%+</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Matches Grid -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="material-icons align-middle">compare</i> Matches Found
                        <span class="badge bg-light text-dark ms-2">{{ $matches->total() }}</span>
                    </h5>
                    <div>
                        <small class="text-light">Showing {{ $matches->count() }} of {{ $matches->total() }}</small>
                    </div>
                </div>
                <div class="card-body">
                    @if($matches->count() > 0 || session('new_matches'))
                        <!-- New Matches Alert -->
                        @if(session('new_matches'))
                        <div class="alert alert-success mb-4">
                            <h6><i class="material-icons align-middle">new_releases</i> New Matches Found!</h6>
                            <p>{{ session('success') }}</p>
                            <div class="d-flex gap-2">
                                <a href="#newMatches" class="btn btn-sm btn-success">View New Matches</a>
                                <a href="{{ route('forensic.face.match') }}" class="btn btn-sm btn-outline-success">View All</a>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            @foreach($matches as $match)
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 shadow-sm border-{{ $match->status == 'verified' ? 'success' : ($match->status == 'rejected' ? 'danger' : 'warning') }}">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                        <small class="text-muted">Match #{{ $match->id }}</small>
                                        {!! $match->statusBadge !!}
                                    </div>
                                    
                                    <div class="card-body p-3">
                                        <!-- Images Comparison -->
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="text-center mb-2">
                                                    <small class="text-muted">Reference</small>
                                                </div>
                                                <div class="face-image-container">
                                                    @if(Storage::disk('public')->exists($match->reference_image_path))
                                                        <img src="{{ asset('storage/' . $match->reference_image_path) }}" 
                                                             class="img-fluid rounded border"
                                                             alt="Reference Face"
                                                             style="height: 120px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                                             style="height: 120px;">
                                                            <i class="material-icons text-white">image_not_supported</i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center mb-2">
                                                    <small class="text-muted">Matched</small>
                                                </div>
                                                <div class="face-image-container">
                                                    @if($match->media && Storage::disk('public')->exists($match->matched_image_path))
                                                        <img src="{{ asset('storage/' . $match->matched_image_path) }}" 
                                                             class="img-fluid rounded border"
                                                             alt="Matched Face"
                                                             style="height: 120px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                                             style="height: 120px;">
                                                            <i class="material-icons text-white">image_not_supported</i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Match Details -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong>Confidence:</strong>
                                                {!! $match->confidenceBadge !!}
                                            </div>
                                            
                                            @if($match->complaint)
                                            <p class="mb-1">
                                                <strong>Case:</strong> 
                                                <a href="{{ route('forensic.case.details', $match->complaint_id) }}" 
                                                   class="text-decoration-none">
                                                    {{ $match->complaint->track_id }}
                                                </a>
                                            </p>
                                            <p class="mb-1 small text-truncate" title="{{ $match->complaint->subject }}">
                                                {{ \Illuminate\Support\Str::limit($match->complaint->subject, 30) }}
                                            </p>
                                            @endif

                                            <p class="mb-1 small text-muted">
                                                <i class="material-icons align-middle" style="font-size: 14px;">person</i>
                                                By: {{ $match->analyst->name ?? 'System' }}
                                            </p>
                                            <p class="mb-0 small text-muted">
                                                <i class="material-icons align-middle" style="font-size: 14px;">schedule</i>
                                                {{ $match->created_at->format('M d, H:i') }}
                                            </p>
                                        </div>

                                        <!-- Actions -->
                                        <div class="d-grid gap-2">
                                            @if($match->status == 'pending')
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('forensic.face.verify', $match->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="material-icons align-middle">check</i> Verify
                                                    </button>
                                                </form>
                                                <form action="{{ route('forensic.face.reject', $match->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Reject this match?')">
                                                        <i class="material-icons align-middle">close</i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                            
                                            <a href="{{ route('forensic.face.view', $match->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="material-icons align-middle">visibility</i> Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($matches->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <small class="text-muted">
                                    Showing {{ $matches->firstItem() }} to {{ $matches->lastItem() }} of {{ $matches->total() }} matches
                                </small>
                            </div>
                            <div>
                                {{ $matches->withQueryString()->links() }}
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="material-icons" style="font-size: 64px;">face</i>
                                <h4 class="mt-3">No Matches Found</h4>
                                <p>Upload a reference face image to start matching</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.face-image-container {
    height: 120px;
    overflow: hidden;
    border-radius: 8px;
    border: 2px solid #dee2e6;
}

.face-image-container img {
    transition: transform 0.3s;
}

.face-image-container:hover img {
    transform: scale(1.05);
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>

<script>
// Image preview
document.getElementById('faceImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('previewImage');
    const previewDiv = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewDiv.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        previewDiv.classList.add('d-none');
    }
});

// Form submission loading
document.getElementById('faceUploadForm').addEventListener('submit', function() {
    const btn = document.getElementById('uploadBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('btnSpinner');
    
    btn.disabled = true;
    btnText.textContent = 'Processing...';
    spinner.classList.remove('d-none');
});

// Filter functions
function applyFilter() {
    const status = document.getElementById('statusFilter').value;
    const confidence = document.getElementById('confidenceFilter').value;
    const url = new URL(window.location.href);
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    if (confidence) {
        url.searchParams.set('min_confidence', confidence);
    } else {
        url.searchParams.delete('min_confidence');
    }
    
    window.location.href = url.toString();
}

function clearFilters() {
    window.location.href = '{{ route("forensic.face.match") }}';
}

// Auto-scroll to new matches
@if(session('new_matches'))
document.addEventListener('DOMContentLoaded', function() {
    const newMatchesSection = document.querySelector('.alert-success');
    if (newMatchesSection) {
        newMatchesSection.scrollIntoView({ behavior: 'smooth' });
    }
});
@endif
</script>
@endsection