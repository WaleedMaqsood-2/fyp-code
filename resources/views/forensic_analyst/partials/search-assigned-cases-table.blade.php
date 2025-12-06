<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-nowrap">Case ID</th>
                    <th class="text-nowrap">Track ID</th>
                    <th>Title</th>
                    <th class="text-nowrap">Forwarded By (Officer)</th>
                    <th class="text-nowrap">Priority</th>
                    <th class="text-nowrap">Status</th>
                    <th class="text-nowrap">Date Forwarded</th>
                    <th class="text-nowrap">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignedCases as $case)
                   @php
                    // Priority badge styling
                    $badgeClass = match(strtolower($case->severity ?? 'low')) {
                        'high' => 'bg-danger text-white',
                        'medium' => 'bg-warning text-dark',
                        'low' => 'bg-success text-white',
                        default => 'bg-secondary text-white'
                    };
                    
                    // Status badge styling
                    $statusBadgeClass = match(strtolower($case->status ?? '')) {
                        'received' => 'bg-primary text-white',
                        'pending' => 'bg-warning text-dark',
                        'in_progress', 'in progress' => 'bg-info text-white',
                        'forwarded' => 'bg-primary text-white',
                        'reviewing' => 'bg-secondary text-white',
                        'under_analysis', 'under analysis' => 'bg-info text-white',
                        'completed' => 'bg-success text-white',
                        'rejected' => 'bg-danger text-white',
                        'resolved' => 'bg-success text-white',
                        default => 'bg-dark text-white',
                    };
                    
                    // Format status text for display
                    $statusText = ucwords(str_replace('_', ' ', $case->status));
                    $severityText = ucfirst($case->severity ?? 'low');
                @endphp
                    <tr>
                        <td class="text-nowrap fw-bold">#{{ $case->id }}</td>
                        <td class="text-nowrap">{{ $case->track_id }}</td>
                        <td class="text-start px-3">
                            <div class="d-flex flex-column">
                                <span class="fw-medium">{{ Str::limit($case->subject, 50) }}</span>
                                @if(strlen($case->subject) > 50)
                                    <small class="text-muted" title="{{ $case->subject }}">{{ Str::limit($case->subject, 100) }}</small>
                                @endif
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <span class="d-inline-block">
                                {{ $case->officer->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <span class="badge {{ $badgeClass }} px-3 py-2">
                                {{ $severityText }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <span class="badge {{ $statusBadgeClass }} px-3 py-2">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex flex-column">
                                <span>{{ $case->created_at->format('d M Y') }}</span>
                                <small class="text-muted">{{ $case->created_at->format('h:i A') }}</small>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('forensic.case.details', $case->id) }}" class="btn btn-sm btn-outline-primary px-3">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                  <tr>
                        <td colspan="8" class="text-center text-muted py-4 fw-bold">
                            <i class="bi bi-inbox fs-4 me-2"></i>
                            No forwarded cases found
                        </td>
                    </tr>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($assignedCases->hasPages())
        <div class="border-top pt-3 px-3">
            {{ $assignedCases->onEachSide(1)->links() }}
        </div>
    @endif
</div><style>
/* Table responsive fixes */
.table-responsive {
    max-height: 600px;
    overflow-y: auto;
}

/* Table header sticky */
.table thead th {
    position: sticky;
    top: 0;
    background-color: #f8f9fa;
    z-index: 10;
}

/* Better row spacing */
.table tbody tr {
    height: 70px;
}

.table tbody td {
    vertical-align: middle;
}

/* Badge styling */
.badge {
    font-size: 0.85em;
    font-weight: 500;
    letter-spacing: 0.5px;
    min-width: 80px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .badge {
        font-size: 0.8em;
        min-width: 70px;
        padding: 0.25em 0.5em !important;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
}
</style>