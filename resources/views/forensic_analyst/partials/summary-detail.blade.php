{{-- resources/views/forensic_analyst/summary-detail.blade.php --}}
@extends('forensic_analyst.layouts.app')
@section('title', 'Summary Details')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-primary mb-4">
        <span class="material-icons align-middle">summarize</span> Summary Details
    </h2>

    <div class="row">
        <!-- Case Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Case Information</h6>
                </div>
                <div class="card-body">
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
                            <th>Status:</th>
                            <td>{!! $summary->statusBadge !!}</td>
                        </tr>
                        <tr>
                            <th>Generated:</th>
                            <td>{{ $summary->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>By:</th>
                            <td>{{ $summary->user->name ?? 'System' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($verification)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Verification Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="120">Approved By:</th>
                            <td>{{ $verification->approver->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <th>Approved On:</th>
                            <td>{{ $verification->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>{!! $verification->statusBadge !!}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Summary Content -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">AI Generated Summary</h6>
                </div>
                <div class="card-body">
                    <div class="summary-content" style="max-height: 400px; overflow-y: auto;">
                        <p style="white-space: pre-wrap; line-height: 1.6;">{{ $summary->summary_text }}</p>
                    </div>
                </div>
            </div>
            
            @if($verification)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Approved/Verified Summary</h6>
                </div>
                <div class="card-body">
                    <div class="verified-content" style="max-height: 400px; overflow-y: auto;">
                        <p style="white-space: pre-wrap; line-height: 1.6;">{{ $verification->summary_text }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('forensic.summary') }}" class="btn btn-secondary">
                    <i class="material-icons align-middle">arrow_back</i> Back to List
                </a>
                <a href="{{ route('forensic.summary.approved') }}" class="btn btn-outline-primary">
                    View All Approved
                </a>
            </div>
        </div>
    </div>
</div>
@endsection