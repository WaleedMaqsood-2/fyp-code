@extends('forensic_analyst.layouts.app')

@section('title','Assigned Cases')


@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0">
            <i class="bi bi-folder2"></i> Assigned Cases
        </h4>
        <span class="text-muted">View and analyze your forwarded cases</span>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <!-- Search -->
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search by Case ID, Title, or Officer">
                </div>

                <!-- Filter by Status -->
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="under_analysis">Under Analysis</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <!-- Filter by Priority -->
                <div class="col-md-3">
                    <select id="priorityFilter" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div class="col-md-2 text-end">
                    <button class="btn btn-outline-secondary w-100" id="resetFilters">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Cases Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-bordered align-middle text-center" id="casesTable">
                <thead class="table-light">
                    <tr>
                        <th>Case ID</th>
                        <th>Track ID</th>
                        <th>Title</th>
                        <th>Assigned By (Officer)</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date Forwarded</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignedCases as $case)
                    <tr>
                        <td>#{{ $case->id }}</td>
                        <td>{{ $case->track_id }}</td>
                        <td>{{ $case->subject }}</td>
                        <td>{{ $case->officer->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $case->severity == 'high' ? 'danger' : ($case->severity == 'medium' ? 'warning' : 'success') }}">
                                {{ ucfirst($case->severity) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $case->status == 'completed' ? 'success' : ($case->status == 'under_analysis' ? 'info' : 'secondary') }}">
                                {{ ucfirst($case->status) }}
                            </span>
                        </td>
                        <td>{{ $case->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('forensic.case.details', $case->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-muted">No assigned cases found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const resetFilters = document.getElementById('resetFilters');
    const rows = document.querySelectorAll('#casesTable tbody tr');

    function filterTable() {
        const searchValue = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const priorityValue = priorityFilter.value.toLowerCase();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matchesSearch = text.includes(searchValue);
            const matchesStatus = !statusValue || row.textContent.toLowerCase().includes(statusValue);
            const matchesPriority = !priorityValue || row.textContent.toLowerCase().includes(priorityValue);

            row.style.display = (matchesSearch && matchesStatus && matchesPriority) ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', filterTable);
    statusFilter.addEventListener('change', filterTable);
    priorityFilter.addEventListener('change', filterTable);
    resetFilters.addEventListener('click', () => {
        searchInput.value = '';
        statusFilter.value = '';
        priorityFilter.value = '';
        filterTable();
    });
});
</script>
@endsection