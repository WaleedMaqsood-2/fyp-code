@extends('forensic_analyst.layouts.app')
@section('title', 'AI Summary Approval')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-primary mb-4">
        <span class="material-icons align-middle">approval</span> AI Summary Approval
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('forensic.summary') }}" class="btn btn-outline-secondary">
                            <i class="material-icons align-middle">refresh</i>
                        </a>
                        <a href="{{ route('forensic.summary.approved') }}" class="btn btn-success">
                            <i class="material-icons align-middle">history</i> View Approved
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="material-icons text-primary">summarize</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Total Summaries</h6>
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
                                    <i class="material-icons text-warning">pending_actions</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Pending Approval</h6>
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
                                    <i class="material-icons text-success">check_circle</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Approved</h6>
                                    <p class="mb-0 fw-bold">{{ $stats['approved'] }}</p>
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
                                    <i class="material-icons text-danger">cancel</i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Rejected</h6>
                                    <p class="mb-0 fw-bold">{{ $stats['rejected'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summaries List -->
            @if($summaries->count() > 0)
                @foreach($summaries as $summary)
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-warning">
                                <span class="material-icons align-middle">pending</span>
                                Case #{{ $summary->complaint->track_id ?? 'N/A' }} — {{ $summary->complaint->subject ?? 'Untitled Case' }}
                            </h5>
                            <div>
                                <span class="badge bg-warning">Pending Approval</span>
                                <small class="text-muted ms-2">
                                    Generated: {{ $summary->created_at->format('M d, Y H:i') }}
                                </small>
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
                                        <td>{{ $summary->complaint->track_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Title:</th>
                                        <td>{{ $summary->complaint->subject ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Generated By:</th>
                                        <td>
                                            {{ $summary->user->name ?? 'System' }}
                                            <small class="text-muted">(AI System)</small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Summary Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="120">Status:</th>
                                        <td>{!! $summary->statusBadge !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Word Count:</th>
                                        <td>{{ str_word_count($summary->summary_text) }} words</td>
                                    </tr>
                                    <tr>
                                        <th>Char Count:</th>
                                        <td>{{ strlen($summary->summary_text) }} characters</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- AI Generated Summary -->
                        <div class="mb-4">
                            <h6 class="text-primary">
                                <i class="material-icons align-middle">smart_toy</i> AI-Generated Summary
                            </h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="ai-summary-content" style="max-height: 200px; overflow-y: auto;">
                                        <p style="white-space: pre-wrap;">{{ $summary->summary_text }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Form -->
                        <form method="POST" action="{{ route('forensic.summary.update') }}">
                            @csrf
                            <input type="hidden" name="complaint_id" value="{{ $summary->complaint_id }}">
                            
                            <div class="mb-4">
                                <h6 class="text-success">
                                    <i class="material-icons align-middle">edit_note</i> Edit/Approve Summary
                                </h6>
                                <div class="card border-success">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Refined Summary</label>
                                            <textarea class="form-control" name="approved_summary" rows="6" 
                                                      placeholder="Review and edit the AI-generated summary...">{{ $summary->summary_text }}</textarea>
                                            <small class="text-muted">You can edit, refine, or completely rewrite the summary.</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Feedback/Comments</label>
                                            <textarea class="form-control" name="feedback" rows="2" 
                                                      placeholder="Add any comments or feedback about this summary..."></textarea>
                                        </div>
                                        
                                        <div class="d-flex gap-3 mt-4">
                                            <button type="submit" name="action" value="approve" class="btn btn-success flex-fill">
                                                <i class="material-icons align-middle">check_circle</i> Approve Summary
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger flex-fill">
                                                <i class="material-icons align-middle">cancel</i> Reject Summary
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                @if($summaries->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <small class="text-muted">
                            Showing {{ $summaries->firstItem() }} to {{ $summaries->lastItem() }} of {{ $summaries->total() }} summaries
                        </small>
                    </div>
                    <div>
                        {{ $summaries->links() }}
                    </div>
                </div>
                @endif
            @else
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="material-icons" style="font-size: 64px;">check_circle</i>
                    <h4 class="mt-3">All Caught Up!</h4>
                    <p>No pending summaries for approval</p>
                    @if(request('search'))
                    <p class="mt-3">
                        <a href="{{ route('forensic.summary') }}" class="btn btn-outline-primary">
                            Clear Search
                        </a>
                    </p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection