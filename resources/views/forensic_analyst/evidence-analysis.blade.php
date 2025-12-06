@extends('forensic_analyst.layouts.app')
@section('title','Evidence Analysis & AI Tools')

@section('content')
<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-white p-3 shadow-sm rounded">
            <li class="breadcrumb-item"><a href="{{ route('forensic.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('forensic.case.details', $case->id) }}">Case #{{ $case->id }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Evidence Analysis</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Evidence Analysis</h3>
        <span class="badge bg-dark px-3 py-2">{{ strtoupper($evidence->type) }}</span>
    </div>

    <div class="row">

        <!-- Left Panel (Evidence Preview) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Evidence Preview</h5>
                </div>
                <div class="card-body text-center">

                    @if($evidence->isImage())
                        <img src="{{ asset('storage/' . $evidence->path) }}" class="img-fluid rounded shadow"/>
                    
                    @elseif($evidence->isVideo())
                        <video controls class="w-100 rounded">
                            <source src="{{ asset('storage/' . $evidence->path) }}" type="video/mp4">
                        </video>

                    @elseif($evidence->isAudio())
                        <audio controls class="w-100 mt-4">
                            <source src="{{ asset('storage/' . $evidence->path) }}">
                        </audio>

                    @else
                        <a href="{{ asset('storage/' . $evidence->path) }}" class="btn btn-dark" download>
                            Download Document
                        </a>
                    @endif

                </div>
            </div>
        </div>

        <!-- Right Panel (AI Tools & Editors) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">AI Tools & Forensic Actions</h5>
                </div>

                <div class="card-body">

                    <!-- AI Buttons -->
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if($evidence->isAudio())
                            <button class="btn btn-primary"><i class="bi bi-soundwave"></i> Generate Transcription</button>
                            <button class="btn btn-info"><i class="bi bi-scissors"></i> Segment Audio</button>
                        @endif

                        @if($evidence->isImage() || $evidence->isVideo())
                            <button class="btn btn-warning"><i class="bi bi-person-square"></i> Detect Faces</button>
                            <button class="btn btn-dark"><i class="bi bi-people"></i> Match Multiple Faces</button>
                        @endif

                        <button class="btn btn-success"><i class="bi bi-stars"></i> Generate Summary</button>
                    </div>

                    <!-- AI Output Tabs -->
                    <ul class="nav nav-tabs" id="analysisTabs" role="tablist">
                        @if($evidence->isAudio())
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#transcription">Transcription</button>
                            </li>
                        @endif

                        <li class="nav-item">
                            <button class="nav-link {{ !$evidence->isAudio() ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#summary">Summary</button>
                        </li>

                        @if($evidence->isImage() || $evidence->isVideo())
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#faces">Face Detection</button>
                            </li>
                        @endif
                    </ul>

                    <div class="tab-content border p-3 rounded-bottom shadow-sm">

                        <!-- Transcription Editor -->
                        @if($evidence->isAudio())
                        <div class="tab-pane fade show active" id="transcription">
                            <label class="fw-bold mb-2">AI Transcription (Editable):</label>
                            <textarea class="form-control" rows="10">{{ $evidence->transcription ?? '' }}</textarea>
                        </div>
                        @endif

                        <!-- AI Summary -->
                        <div class="tab-pane fade {{ !$evidence->isAudio() ? 'show active' : '' }}" id="summary">
                            <label class="fw-bold mb-2">AI-Generated Summary:</label>
                            <textarea class="form-control" rows="8">{{ $evidence->summary ?? '' }}</textarea>
                        </div>

                        <!-- Face Detection Results -->
                        @if($evidence->isImage() || $evidence->isVideo())
                        <div class="tab-pane fade" id="faces">
                            <h6 class="fw-bold mb-3">Detected Faces:</h6>
                            <div class="row">
                                @forelse($faces as $face)
                                    <div class="col-4 mb-3">
                                        <img src="{{ asset('storage/faces/' . $face) }}" class="img-fluid rounded shadow"/>
                                    </div>
                                @empty
                                    <p class="text-muted">No faces detected yet.</p>
                                @endforelse
                            </div>
                        </div>
                        @endif

                    </div>

                    <!-- Submit Final Results -->
                    <div class="text-end mt-3">
                        <button class="btn btn-success px-4 py-2">
                            <i class="bi bi-check2-circle"></i> Submit Forensic Results
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
@endsection
