@extends('forensic_analyst.layouts.app')
@section('title', 'Audio/Video Evidence Segmentation')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-primary mb-4">Audio/Video Evidence Segmentation</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3 text-primary"><span class="material-icons align-middle">audiotrack</span> Evidence List</h5>
            
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
                                   placeholder="Search by Case ID, Description or Filename..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <select class="form-select" id="filterMediaType">
                            <option value="">All Media Types</option>
                            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video Only</option>
                            <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio Only</option>
                        </select>
                        <button class="btn btn-outline-secondary" id="resetFilters">
                            <i class="material-icons">refresh</i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Tags -->
            @if(request()->hasAny(['search', 'type']))
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
                <a href="{{ route('forensic.audio-video') }}" class="btn btn-sm btn-outline-danger">
                    Clear All Filters
                </a>
            </div>
            @endif

            <!-- Statistics -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons text-primary">video_library</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Total Media</h6>
                                    <p class="mb-0 fw-bold">{{ $evidences->count() }}</p>
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
                                    <h6 class="mb-0">Videos</h6>
                                    <p class="mb-0 fw-bold">{{ $evidences->where('file_type', 'video')->count() }}</p>
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
                                    <h6 class="mb-0">Audios</h6>
                                    <p class="mb-0 fw-bold">{{ $evidences->where('file_type', 'audio')->count() }}</p>
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
                                    <i class="material-icons text-info">content_cut</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Segments</h6>
                                    @php
                                        $totalSegments = 0;
                                        foreach($evidences as $e) {
                                            $totalSegments += $e->segments->count();
                                        }
                                    @endphp
                                    <p class="mb-0 fw-bold">{{ $totalSegments }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th class="text-center">Media ID</th>
                        <th class="text-center">Case Track ID</th>
                        <th>Type</th>
                        <th>Filename</th>
                        <th>Description</th>
                        <th>Preview</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evidences as $e)
                    <tr>
                        <td>{{ $e->id }}</td>
                        <td>{{ $e->track_id }}</td>
                        <td>
                            <span class="badge {{ $e->file_type == 'video' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($e->file_type) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="material-icons me-2">{{ $e->file_type == 'video' ? 'videocam' : 'audiotrack' }}</i>
                                <span title="{{ $e->file_path }}">
                                    {{ \Illuminate\Support\Str::limit(basename($e->file_path), 20, '...') }}
                                </span>
                            </div>
                        </td>
                        <td>{{ $e->description ?? 'N/A' }}</td>
                        <td>
                            @php
                                $ext = strtolower(pathinfo($e->file_path, PATHINFO_EXTENSION));
                                $mediaType = $e->file_type;
                                $fileUrl = asset('storage/'.$e->file_path);
                                
                                // MIME type for table preview
                                if($mediaType === 'video'){
                                    $mime = match($ext){
                                        'mp4', 'mov', 'avi', 'mkv' => 'video/mp4',
                                        'webm' => 'video/webm',
                                        default => 'video/mp4'
                                    };
                                } elseif($mediaType === 'audio'){
                                    $mime = match($ext){
                                        'mp3' => 'audio/mpeg',
                                        'wav' => 'audio/wav',
                                        'ogg' => 'audio/ogg',
                                        'webm' => 'audio/webm',
                                        'aac' => 'audio/aac',
                                        'm4a' => 'audio/mp4',
                                        'flac' => 'audio/flac',
                                        default => 'audio/mpeg'
                                    };
                                } else {
                                    $mime = 'video/mp4';
                                }
                            @endphp

                            @if($mediaType === 'video')
                                <div class="preview-thumbnail" 
                                     style="width: 200px; height: 120px; background: #000; border-radius: 4px; overflow: hidden; cursor: pointer;"
                                     onclick="showPreview('{{ $fileUrl }}', 'video', '{{ $mime }}')">
                                    <video style="width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                                        <source src="{{ $fileUrl }}" type="{{ $mime }}">
                                    </video>
                                    <div class="preview-overlay">
                                        <i class="material-icons text-white">play_circle</i>
                                    </div>
                                </div>
                            @elseif($mediaType === 'audio')
                                <div class="audio-preview" 
                                     style="width: 200px; padding: 8px; background: #f8f9fa; border-radius: 4px;"
                                     onclick="showPreview('{{ $fileUrl }}', 'audio', '{{ $mime }}')">
                                    <audio controls style="width: 100%;">
                                        <source src="{{ $fileUrl }}" type="{{ $mime }}">
                                    </audio>
                                </div>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#segmentForm{{ $e->id }}">
                                <i class="material-icons">content_cut</i> Segment
                            </button>
                        </td>
                    </tr>

                    {{-- Segment Form --}}
                    <tr class="collapse" id="segmentForm{{ $e->id }}">
                        <td colspan="7">
                            <form method="POST" action="{{ route('forensic.audio-video.segment') }}">
                                @csrf
                                <input type="hidden" name="evidence_id" value="{{ $e->id }}">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-3">
                                        <label>Start Time (hh:mm:ss)</label>
                                        <input type="text" name="start_time" class="form-control" placeholder="00:00:00" required>
                                        <small class="text-muted">Format: HH:MM:SS</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label>End Time (hh:mm:ss)</label>
                                        <input type="text" name="end_time" class="form-control" placeholder="00:00:30" required>
                                        <small class="text-muted">Format: HH:MM:SS</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Segment Name (Optional)</label>
                                        <input type="text" name="segment_name" class="form-control" placeholder="Enter segment name">
                                    </div>
                                    <div class="col-md-3 mt-2">
                                        <button type="submit" class="btn btn-success mt-4 w-100">
                                            <i class="material-icons">check_circle</i> Create Segment
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>

                    {{-- Show Segments --}}
                    @if($e->segments->isNotEmpty())
                    <tr>
                        <td colspan="7">
                            <div class="segments-section">
                                <h6 class="text-primary mb-3">
                                    <i class="material-icons align-middle">playlist_play</i> 
                                    Segments ({{ $e->segments->count() }})
                                </h6>
                                <div class="row mt-2">
                                    @foreach($e->segments as $seg)
                                    <div class="col-md-3 mb-2">
                                        <div class="card border-info shadow-sm h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge bg-primary">Segment #{{ $loop->iteration }}</span>
                                                    <small class="text-muted">{{ $seg->created_at->format('M d, H:i') }}</small>
                                                </div>
                                                <p class="mb-2 small">
                                                    <i class="material-icons align-middle" style="font-size: 14px;">schedule</i>
                                                    {{ $seg->start_time }} - {{ $seg->end_time }}
                                                </p>
                                                <button type="button" class="btn btn-sm btn-outline-success w-100" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#segmentModal{{ $seg->id }}">
                                                    <i class="material-icons align-middle">play_arrow</i> Play Segment
                                                </button>
                                                <div class="mt-2 text-center">
                                                    <small class="text-muted">
                                                        <i class="material-icons align-middle" style="font-size: 14px;">info</i>
                                                        {{ strtoupper($seg->file_extension ?? 'mp4') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="material-icons" style="font-size: 48px;">folder_off</i>
                                <h5 class="mt-2">No Media Found</h5>
                                @if(request()->hasAny(['search', 'type']))
                                <p>Try changing your search or filter criteria</p>
                                @else
                                <p>No audio/video evidence available for segmentation</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($evidences->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Showing {{ $evidences->firstItem() }} to {{ $evidences->lastItem() }} of {{ $evidences->total() }} entries
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

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Media Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="previewContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Segment Modals (Dynamic) -->
@foreach($evidences as $e)
    @foreach($e->segments as $seg)
    <div class="modal fade" id="segmentModal{{ $seg->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Segment #{{ $seg->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    @php
                        $fileUrl = asset('storage/'.$seg->segment_file);
                        $ext = strtolower($seg->file_extension ?? 'mp4');
                        $mediaType = $seg->media_type ?? 'video';

                        if($mediaType === 'video') {
                            $mime = match($ext) {
                                'mp4' => 'video/mp4',
                                'webm' => 'video/webm',
                                'ogg' => 'video/ogg',
                                'mov' => 'video/quicktime',
                                'avi' => 'video/x-msvideo',
                                'mkv' => 'video/x-matroska',
                                default => 'video/mp4',
                            };
                        } elseif($mediaType === 'audio') {
                            $mime = match($ext) {
                                'mp3' => 'audio/mpeg',
                                'wav' => 'audio/wav',
                                'ogg' => 'audio/ogg',
                                'webm' => 'audio/webm',
                                'aac' => 'audio/aac',
                                'm4a' => 'audio/mp4',
                                'flac' => 'audio/flac',
                                default => 'audio/mpeg',
                            };
                        } else {
                            $mime = 'video/mp4';
                        }
                    @endphp

                    @if($mediaType === 'video')
                        <div style="width: 100%; max-width: 800px; height: 450px; margin: 0 auto; background: #000; border-radius: 8px; overflow: hidden;">
                            <video controls style="width: 100%; height: 100%;">
                                <source src="{{ $fileUrl }}" type="{{ $mime }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-primary me-2">{{ strtoupper($ext) }}</span>
                            <span class="badge bg-secondary me-2">{{ $seg->start_time }} - {{ $seg->end_time }}</span>
                            <span class="badge bg-info">Video Segment</span>
                        </div>
                    @elseif($mediaType === 'audio')
                        <div class="bg-light p-4 rounded" style="max-width: 600px; margin: 0 auto;">
                            <div class="mb-3">
                                <i class="material-icons" style="font-size: 48px; color: #0d6efd;">audiotrack</i>
                            </div>
                            <audio controls style="width: 100%; max-width: 500px;">
                                <source src="{{ $fileUrl }}" type="{{ $mime }}">
                                Your browser does not support the audio tag.
                            </audio>
                            <div class="mt-3">
                                <span class="badge bg-warning me-2">{{ strtoupper($ext) }}</span>
                                <span class="badge bg-secondary me-2">{{ $seg->start_time }} - {{ $seg->end_time }}</span>
                                <span class="badge bg-info">Audio Segment</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ $fileUrl }}" download class="btn btn-primary">
                        <i class="material-icons align-middle">download</i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endforeach

<style>
    .preview-thumbnail {
        position: relative;
        transition: transform 0.2s;
    }
    
    .preview-thumbnail:hover {
        transform: scale(1.02);
    }
    
    .preview-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .preview-thumbnail:hover .preview-overlay {
        opacity: 1;
    }
    
    .audio-preview {
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .audio-preview:hover {
        background: #e9ecef !important;
    }
</style>

<script>
function showPreview(url, type, mime) {
    let content = '';
    if (type === 'video') {
        content = `
            <div style="width: 100%; max-width: 800px; height: 450px; margin: 0 auto; background: #000; border-radius: 8px; overflow: hidden;">
                <video controls autoplay style="width: 100%; height: 100%;">
                    <source src="${url}" type="${mime}">
                    Your browser does not support the video tag.
                </video>
            </div>`;
    } else {
        content = `
            <div class="bg-light p-5 rounded" style="max-width: 600px; margin: 0 auto;">
                <div class="mb-4">
                    <i class="material-icons" style="font-size: 64px; color: #0d6efd;">audiotrack</i>
                </div>
                <audio controls autoplay style="width: 100%; max-width: 500px;">
                    <source src="${url}" type="${mime}">
                    Your browser does not support the audio tag.
                </audio>
            </div>`;
    }
    
    document.getElementById('previewContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

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

// Reset filters
document.getElementById('resetFilters').addEventListener('click', function() {
    window.location.href = '{{ route("forensic.audio-video") }}';
});
</script>
@endsection