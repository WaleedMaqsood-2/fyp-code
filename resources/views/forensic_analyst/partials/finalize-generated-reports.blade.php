{{-- resources/views/forensic_analyst/generated-reports.blade.php --}}
@extends('forensic_analyst.layouts.app')
@section('title', 'Generated Reports')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-primary mb-4">
        <i class="material-icons align-middle">history</i> Generated Reports
    </h2>

    <!-- Search Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('forensic.finalize.generated') }}" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="material-icons">search</i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search generated reports..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('forensic.finalize') }}" class="btn btn-outline-primary">
                            <i class="material-icons align-middle">arrow_back</i> Back to Cases
                        </a>
                    </div>
                </div>
            </div>

            @if($reports->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Case Title</th>
                            <th>Report File</th>
                            <th>Generated On</th>
                            <th>File Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        @php
                            $fileSize = Storage::disk('public')->exists($report->file_path) 
                                ? number_format(Storage::disk('public')->size($report->file_path) / 1024, 2) . ' KB'
                                : 'N/A';
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $report->complaint->track_id ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ $report->complaint->subject ?? 'Untitled' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="material-icons text-primary me-2">picture_as_pdf</i>
                                    <span>{{ basename($report->file_path) }}</span>
                                </div>
                            </td>
                            <td>
                                {{ $report->exported_at->format('M d, Y H:i') }}
                                <br>
                                <small class="text-muted">{{ $report->exported_at->diffForHumans() }}</small>
                            </td>
                            <td>{{ $fileSize }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('forensic.finalize.download', $report->id) }}" 
                                       class="btn btn-sm btn-success">
                                        <i class="material-icons align-middle">download</i>
                                    </a>
                                    <a href="{{ route('forensic.finalize.view', $report->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="material-icons align-middle">visibility</i>
                                    </a>
                                  {{-- Delete button کو form میں wrap کریں --}}
<form action="{{ route('forensic.finalize.delete', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this report?')">
    @csrf
    @method('DELETE') {{-- یا POST --}}
    <button type="submit" class="btn btn-sm btn-danger">
        <i class="material-icons align-middle">delete</i>
    </button>
</form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($reports->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <small class="text-muted">
                        Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} reports
                    </small>
                </div>
                <div>
                    {{ $reports->links() }}
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="material-icons" style="font-size: 64px;">history</i>
                    <h4 class="mt-3">No Generated Reports</h4>
                    <p>No reports have been generated yet.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmDelete(reportId) {
    if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        // POST request send کریں
        fetch('{{ url("forensic/finalize/delete") }}/' + reportId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ _method: 'DELETE' })
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
</script>
@endsection