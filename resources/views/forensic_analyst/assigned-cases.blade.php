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

    <!-- Filters and Search Form -->
    <form method="GET" action="{{ route('forensic.assigned-cases') }}" id="filtersForm">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">

                    <!-- Search -->
                    <div class="col-md-4">
                        <label class="fw-bold mb-1">Search</label>
                        <div class="input-group">
                            <input type="text" id="searchInput" name="search" class="form-control" placeholder="🔍 Search by Case ID, Track ID, Title, or Officer" value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit" id="searchBtn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-3">
                        <label class="fw-bold mb-1">Status</label>
                        <select id="statusFilter" name="status" class="form-select border-primary" onchange="document.getElementById('filtersForm').submit()">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ strtolower($status) }}" {{ request('status') == strtolower($status) ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_',' ',$status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority -->
                    <div class="col-md-3">
                        <label class="fw-bold mb-1">Priority</label>
                        <select id="priorityFilter" name="priority" class="form-select border-warning" onchange="document.getElementById('filtersForm').submit()">
                            <option value="">All Priorities</option>
                            @foreach($severties as $severtie)
                                <option value="{{ strtolower($severtie) }}" {{ request('priority') == strtolower($severtie) ? 'selected' : '' }}>
                                    {{ ucfirst($severtie) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset -->
                    <div class="col-md-2 text-end">
                        <label class="fw-bold mb-1">&nbsp;</label>
                        <a href="{{ route('forensic.assigned-cases') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <!-- Assigned Cases Table -->
    <div class="card shadow-sm border-0" id="casesTableContainer">
        @include('forensic_analyst.partials.search-assigned-cases-table', ['assignedCases' => $assignedCases])
    </div>

    <!-- Show All / Hide All Button - Only show if there are multiple pages -->
    @if($assignedCases->hasPages())
    <div class="text-center my-4" id="showAllContainer">
        <button class="btn btn-outline-primary" id="toggleShowAllBtn">
            <i class="bi bi-table"></i> Show All Cases ({{ $totalCases ?? 0 }})
        </button>
    </div>
    @endif

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const searchBtn = document.getElementById("searchBtn");
    const form = document.getElementById("filtersForm");
    const casesTableContainer = document.getElementById("casesTableContainer");
    const toggleBtn = document.getElementById("toggleShowAllBtn");
    let showAll = false;
    let totalCases = {{ $totalCases ?? 0 }};

    // Loader function
    function showLoading() {
        casesTableContainer.innerHTML = `
            <div class="card-body">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading cases...</p>
                </div>
            </div>
        `;
    }

    // Function to fetch all cases (without pagination)
    function fetchAllCases() {
        showLoading();
        
        const formData = new FormData(form);
        formData.append('show_all', '1'); // Add show_all parameter
        
        const params = new URLSearchParams();
        for(const pair of formData.entries()){
            if(pair[1]) params.append(pair[0], pair[1]);
        }

        fetch(`{{ route('forensic.assigned-cases') }}?${params.toString()}&ajax=1`, {
            method: 'GET',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            casesTableContainer.innerHTML = html;
            if(toggleBtn) {
                toggleBtn.innerHTML = `<i class="bi bi-table"></i> Hide (Show Paginated)`;
                toggleBtn.classList.remove('btn-outline-primary');
                toggleBtn.classList.add('btn-outline-danger');
            }
            showAll = true;
        })
        .catch(err => {
            console.error(err);
            casesTableContainer.innerHTML = `
                <div class="card-body">
                    <div class="alert alert-danger">Error loading cases. Please try again.</div>
                </div>
            `;
        });
    }

    // Function to show paginated view
    function showPaginatedView() {
        showLoading();
        
        const formData = new FormData(form);
        formData.delete('show_all'); // Remove show_all parameter
        
        const params = new URLSearchParams();
        for(const pair of formData.entries()){
            if(pair[1]) params.append(pair[0], pair[1]);
        }

        fetch(`{{ route('forensic.assigned-cases') }}?${params.toString()}&ajax=1`, {
            method: 'GET',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            casesTableContainer.innerHTML = html;
            if(toggleBtn) {
                toggleBtn.innerHTML = `<i class="bi bi-table"></i> Show All Cases (${totalCases})`;
                toggleBtn.classList.remove('btn-outline-danger');
                toggleBtn.classList.add('btn-outline-primary');
            }
            showAll = false;
        })
        .catch(err => {
            console.error(err);
            casesTableContainer.innerHTML = `
                <div class="card-body">
                    <div class="alert alert-danger">Error loading cases. Please try again.</div>
                </div>
            `;
        });
    }

    // Toggle Show All / Hide All
    if(toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            if(showAll) {
                showPaginatedView();
            } else {
                fetchAllCases();
            }
        });
    }

    // AJAX form submission for regular searches
    searchBtn.addEventListener("click", (e) => {
        e.preventDefault();
        showPaginatedView();
    });
// Filters change with loader
document.getElementById("statusFilter").addEventListener("change", () => {
    showLoading();             // Show loader
    form.submit();             // Submit form
});

document.getElementById("priorityFilter").addEventListener("change", () => {
    showLoading();             // Show loader
    form.submit();             // Submit form
});

    searchInput.addEventListener("keyup", e => { 
        if(e.key === "Enter") {
            e.preventDefault();
            showPaginatedView();
        }
    });
});
</script>
@endsection