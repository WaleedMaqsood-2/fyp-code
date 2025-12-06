@extends('forensic_analyst.layouts.app')
@section('title','Case Details & Evidence Review')

@section('content')
<div class="container py-4">

{{-- ===================== SUCCESS ALERT ===================== --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ===================== PAGE HEADER ===================== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-primary mb-0 fw-bold">
        <i class="material-icons align-middle">science</i> Case Details & AI Review
    </h3>
    <a href="{{ route('forensic.assigned-cases') }}" class="btn btn-outline-secondary">
        <i class="material-icons align-middle">arrow_back</i> Back to List
    </a>
</div>

{{-- ===================== CASE SUMMARY ===================== --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0 fw-bold">
            <i class="material-icons align-middle">info</i> Case Summary
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Case ID:</strong> #{{ $case->id }}</p>
                <p><strong>Track ID:</strong> {{ $case->track_id ?? 'N/A' }}</p>
                <p><strong>Title:</strong> {{ $case->subject ?? 'N/A' }}</p>
                <p><strong>Filed By:</strong> {{ $case->user->name ?? 'Unknown' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $case->status == 'forwarded' ? 'warning' : 'success' }}">
                        {{ ucfirst($case->status) }}
                    </span>
                </p>
                <p><strong>Incident Type:</strong> {{ $case->incident_type ?? 'N/A' }}</p>
                <p><strong>Location:</strong> {{ $case->location ?? 'N/A' }}</p>
                <p><strong>Received On:</strong> {{ $case->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ===================== UPLOADED EVIDENCE ===================== --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="material-icons align-middle">perm_media</i> Uploaded Evidence
            <span class="badge bg-light text-dark ms-2">{{ $evidences->count() }}</span>
        </h5>

        <div>
            {{-- FACE DETECTION --}}
            <form action="{{ route('forensic.face.match', $case->id) }}" method="POST" class="d-inline aiForm">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light aiBtn">
                    <span class="label"><i class="material-icons align-middle">face</i> Detect Faces</span>
                    <span class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </form>

            {{-- TRANSCRIPTION --}}
            <form action="{{ route('forensic.transcript', $case->id) }}" method="POST" class="d-inline aiForm">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light aiBtn">
                    <span class="label"><i class="material-icons align-middle">mic</i> Transcription</span>
                    <span class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </form>
        </div>
    </div>

    <div class="card-body">
    @if($evidences->isEmpty())
        <p class="text-muted">No digital evidence uploaded.</p>
    @else
        <div class="row">
            @foreach($evidences as $evidence)
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-0">

                    @php
                        $ext = strtolower(pathinfo($evidence->file_path, PATHINFO_EXTENSION));
                    @endphp

                    {{-- IMAGE --}}
                    @if(in_array($ext, ['jpg','jpeg','png','gif']))
                    <a href="{{ asset('storage/'.$evidence->file_path) }}" target="_blank">
                        <img src="{{ asset('storage/'.$evidence->file_path) }}" 
                             class="card-img-top" 
                             style="height:220px;object-fit:cover"
                             alt="Evidence Image">
                    </a>

                    {{-- VIDEO --}}
                    @elseif(in_array($ext, ['mp4','avi','mov']))
                    <div class="card-img-top" style="height:220px;background:#000;">
                        <video controls class="w-100 h-100">
                            <source src="{{ asset('storage/'.$evidence->file_path) }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>

                    {{-- AUDIO --}}
                    @elseif(in_array($ext, ['mp3','wav','ogg']))
                    <div class="card-body text-center">
                        <i class="material-icons text-primary" style="font-size:48px;">audiotrack</i>
                        <p class="mt-2 small">{{ basename($evidence->file_path) }}</p>
                        <audio controls class="w-100">
                            <source src="{{ asset('storage/'.$evidence->file_path) }}">
                            Your browser does not support the audio element.
                        </audio>
                    </div>

                    {{-- OTHER FILES --}}
                    @else
                    <div class="card-body text-center">
                        <i class="material-icons text-secondary" style="font-size:48px;">insert_drive_file</i>
                        <p class="mt-2 small">{{ basename($evidence->file_path) }}</p>
                        <a href="{{ asset('storage/'.$evidence->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            View File
                        </a>
                    </div>
                    @endif

                    <div class="card-footer text-muted small">
                        <div class="d-flex justify-content-between">
                            <span>{{ ucfirst($evidence->file_type) }}</span>
                            <span>{{ $evidence->created_at->format('d M Y') }}</span>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    @endif
    </div>
</div>

{{-- ===================== AI INSIGHTS (ACCORDION) ===================== --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="material-icons align-middle">psychology</i> AI-Generated Insights
        </h5>

        {{-- GENERATE SUMMARY --}}
        <form action="{{ route('forensic.ai.summarize', $case->id) }}" method="POST" class="d-inline aiForm">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light aiBtn">
                <span class="label"><i class="material-icons align-middle">summarize</i> Generate Summary</span>
                <span class="spinner-border spinner-border-sm d-none"></span>
            </button>
        </form>
    </div>

    <div class="card-body">
        <div class="accordion" id="aiAccordion">

            {{-- TRANSCRIPTION --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="transcriptionHeading">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#transcriptionBody" aria-expanded="true">
                        <strong>
                            <i class="material-icons align-middle">mic</i> Audio/Video Transcription
                            @if($case->transcriptions->count() > 0)
                            <span class="badge bg-success ms-2">{{ $case->transcriptions->count() }}</span>
                            @endif
                        </strong>
                    </button>
                </h2>
                <div id="transcriptionBody" class="accordion-collapse collapse show" data-bs-parent="#aiAccordion">
                    <div class="accordion-body bg-light border">
                        @if($verifiedTranscription)
                            <div class="alert alert-success">
                                <i class="material-icons align-middle">verified</i> Verified Transcript
                            </div>
                            <div class="transcription-content p-3 bg-white rounded">
                                <p style="white-space: pre-wrap;">{{ $verifiedTranscription }}</p>
                            </div>
                        @elseif($case->transcriptions->count() > 0)
                            <div class="transcription-list">
                                @foreach($case->transcriptions as $transcription)
                                <div class="card mb-3">
                                    <div class="card-header bg-light d-flex justify-content-between">
                                        <small>Transcription #{{ $loop->iteration }}</small>
                                        <small>{{ $transcription->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <div class="card-body">
                                        <p style="white-space: pre-wrap;">{{ $transcription->transcript }}</p>
                                        @if($transcription->verifications->isNotEmpty())
                                            @foreach($transcription->verifications as $verification)
                                                <div class="alert alert-info mt-2">
                                                    <small>
                                                        <i class="material-icons align-middle" style="font-size:14px;">edit</i>
                                                        <strong>Verified by Analyst:</strong>
                                                        {{ $verification->updated_at->format('M d, Y H:i') }}
                                                        @if($verification->approved)
                                                        <span class="badge bg-success ms-2">Approved</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No transcription available yet. Use the "Transcription" button above to generate.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="summaryHeading">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#summaryBody">
                        <strong>
                            <i class="material-icons align-middle">summarize</i> AI Summary
                            @if($case->summaries->count() > 0)
                            <span class="badge bg-success ms-2">{{ $case->summaries->count() }}</span>
                            @endif
                        </strong>
                    </button>
                </h2>
                <div id="summaryBody" class="accordion-collapse collapse" data-bs-parent="#aiAccordion">
                    <div class="accordion-body bg-light border">
                        @if($approvedSummary)
                            <div class="alert alert-success">
                                <i class="material-icons align-middle">check_circle</i> Approved Summary
                            </div>
                            <div class="summary-content p-3 bg-white rounded">
                                <p style="white-space: pre-wrap;">{{ $approvedSummary->summary_text }}</p>
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="material-icons align-middle" style="font-size:14px;">person</i>
                                        Generated by: {{ $approvedSummary->user->name ?? 'System' }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="material-icons align-middle" style="font-size:14px;">schedule</i>
                                        {{ $approvedSummary->updated_at->format('M d, Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        @elseif($case->summaries->count() > 0)
                            <div class="summary-list">
                                @foreach($case->summaries as $summary)
                                <div class="card mb-3">
                                    <div class="card-header bg-light d-flex justify-content-between">
                                        <div>
                                            <small>Summary #{{ $loop->iteration }}</small>
                                            <span class="badge bg-{{ $summary->status == 'approved' ? 'success' : ($summary->status == 'rejected' ? 'danger' : 'warning') }} ms-2">
                                                {{ ucfirst($summary->status) }}
                                            </span>
                                        </div>
                                        <small>{{ $summary->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <div class="card-body">
                                        <p style="white-space: pre-wrap;">{{ $summary->summary_text }}</p>
                                        @if($summary->approved_by)
                                            <small class="text-muted">
                                                <i class="material-icons align-middle" style="font-size:14px;">person</i>
                                                Approved by: {{ $summary->approvedBy->name ?? 'Admin' }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No summary generated yet. Use the "Generate Summary" button above.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ===================== ANALYST REVIEW ===================== --}}
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="material-icons align-middle">rate_review</i> Analyst Review
        </h5>

        {{-- AI REPORT --}}
        <form action="{{ route('forensic.report', $case->id) }}" method="GET">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light">
                <i class="material-icons align-middle">description</i> AI Report
            </button>
        </form>
    </div>

    <div class="card-body">
        {{-- Previous Reviews --}}
        @if($case->latestForensicReview)
        <div class="mb-4">
            <h6><i class="material-icons align-middle">history</i> Previous Review</h6>
            <div class="card bg-light">
                <div class="card-body">
                    <p style="white-space: pre-wrap;">{{ $case->latestForensicReview->findings }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            <i class="material-icons align-middle" style="font-size:14px;">person</i>
                            By: {{ $case->latestForensicReview->analyst->name ?? 'Analyst' }}
                        </small>
                        <small class="text-muted">
                            <i class="material-icons align-middle" style="font-size:14px;">schedule</i>
                            {{ $case->latestForensicReview->created_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- New Review Form --}}
        <form action="{{ route('forensic.submitReview', $case->id) }}" method="POST">
            @csrf

            {{-- Analyst Notes --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Your Analysis Notes</label>
                <textarea name="notes" class="form-control" rows="4" required 
                          placeholder="Enter your detailed analysis and findings..."></textarea>
            </div>

            {{-- Case Status --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Update Case Status</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ $case->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="analyzing" {{ $case->status == 'analyzing' ? 'selected' : '' }}>Analyzing</option>
                    <option value="completed" {{ $case->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="approved" {{ $case->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $case->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-success w-100">
                <i class="material-icons align-middle">send</i> Submit Review
            </button>
        </form>
    </div>
</div>

</div> {{-- container end --}}

<style>
.transcription-content, .summary-content {
    max-height: 300px;
    overflow-y: auto;
    border-left: 4px solid #198754;
    padding-left: 15px;
}

.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    color: #0c63e4;
}

.card-header.bg-light {
    background-color: #f8f9fa !important;
}
</style>

@endsection

@section('scripts')
<script>
document.querySelectorAll('.aiForm').forEach(form => {
    form.addEventListener('submit', function() {
        const btn = form.querySelector('.aiBtn');
        btn.querySelector('.label').classList.add('d-none');
        btn.querySelector('.spinner-border').classList.remove('d-none');
        btn.disabled = true;
    });
});

// Auto-expand accordion sections with content
document.addEventListener('DOMContentLoaded', function() {
    const transcriptionContent = document.querySelector('#transcriptionBody .transcription-content');
    const summaryContent = document.querySelector('#summaryBody .summary-content');
    
    if (transcriptionContent && transcriptionContent.textContent.trim() !== '') {
        const transcriptionAccordion = document.querySelector('#transcriptionBody');
        transcriptionAccordion.classList.add('show');
    }
    
    if (summaryContent && summaryContent.textContent.trim() !== '') {
        const summaryAccordion = document.querySelector('#summaryBody');
        summaryAccordion.classList.add('show');
    }
});
</script>
@endsection