@extends('forensic_analyst.layouts.app')
@section('title', 'Approved Summaries History')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-primary mb-4">
        <span class="material-icons align-middle">history</span> Approved Summaries History
    </h2>

    <div class="card mb-4">
        <div class="card-body">
            <!-- Search Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('forensic.summary.approved') }}" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="material-icons">search</i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search approved summaries..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('forensic.summary') }}" class="btn btn-outline-primary">
                            <i class="material-icons align-middle">arrow_back</i> Back to Pending
                        </a>
                    </div>
                </div>
            </div>

            @if($verifiedSummaries->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Case Title</th>
                            <th>Summary Preview</th>
                            <th>Approved By</th>
                            <th>Approved On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($verifiedSummaries as $verified)
                        <tr>
                            <td>
                                <strong>{{ $verified->complaint->track_id ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                {{ $verified->complaint->subject ?? 'Untitled' }}
                            </td>
                            <td>
                                <div class="summary-preview">
                                    {{ \Illuminate\Support\Str::limit($verified->summary_text, 120) }}
                                </div>
                                <small class="text-muted">
                                    {{ str_word_count($verified->summary_text) }} words
                                </small>
                            </td>
                            <td>
                                {{ $verified->approver->name ?? 'System' }}
                                <br>
                                <small class="text-muted">Role: {{ $verified->approver->role ?? 'Forensic Analyst' }}</small>
                            </td>
                            <td>
                                {{ $verified->updated_at->format('M d, Y H:i') }}
                                <br>
                                <small class="text-muted">{{ $verified->updated_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <a href="{{ route('forensic.summary.detail', $verified->complaint_id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="material-icons align-middle">visibility</i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($verifiedSummaries->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <small class="text-muted">
                        Showing {{ $verifiedSummaries->firstItem() }} to {{ $verifiedSummaries->lastItem() }} of {{ $verifiedSummaries->total() }} approved summaries
                    </small>
                </div>
                <div>
                    {{ $verifiedSummaries->links() }}
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="material-icons" style="font-size: 64px;">history</i>
                    <h4 class="mt-3">No Approved Summaries</h4>
                    <p>You haven't approved any summaries yet</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection