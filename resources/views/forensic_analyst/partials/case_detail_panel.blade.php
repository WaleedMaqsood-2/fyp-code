<!-- Quick Case Overview Panel -->
<div class="tab-content flex-grow-1" style="min-height:220px;">
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
        <h6 class="fw-semibold mb-3">Quick Case Overview</h6>

        @if($cases->isNotEmpty())
            <ul class="list-group list-group-flush">
                @foreach($cases as $case)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">{{ $case->track_id }}</div>
                            <small class="muted">{{ Str::limit($case->subject, 40) }}</small>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            @if($case->severity == 'high')
                                <span class="badge badge-status-high px-2 py-1">High</span>
                            @elseif($case->severity == 'medium')
                                <span class="badge badge-status-medium px-2 py-1">Medium</span>
                            @else
                                <span class="badge badge-success px-2 py-1">Low</span>
                            @endif
                            <button class="btn btn-sm btn-primary" 
                            onclick="window.location='{{ route('forensic.case.details', $case->id) }}'"
                            >
                                Open
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="muted small">No assigned cases for review.</p>
        @endif

        <div class="mt-3 d-flex justify-content-between">
            <a href="
            {{ route('forensic.assigned-cases') }}
            " class="btn btn-outline-primary btn-sm">
                View All Cases
            </a>
            <button class="btn btn-outline-success btn-sm">
                Mark Selected Complete
            </button>
        </div>
    </div>
</div>
