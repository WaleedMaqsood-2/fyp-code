@extends('layouts.master')
@push('styles')
    
<link rel="stylesheet" href="{{ asset('css/admin/manage-complaints.css') }}">
@endpush
@section('title', 'Manage Complaints')
@section('content')
<div class="complaint-container pt-5 mt-2">
    <!-- Header Section -->
    <div class="complaint-header mt-5">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-clipboard-list"></i>
                Complaint Management
            </h1>
            <p class="page-subtitle">View, filter, and manage all submitted complaints effectively</p>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <i class="fas fa-inbox"></i>
                <div>
                    <h3>{{ $complaints->total() }}</h3>
                    <span>Total Complaints</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="alert-section">
        @if ($errors->any())
        <div class="alert alert-danger alert-modern">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="alert-content">
                <h6>Error</h6>
                <p>{{ $errors->first() }}</p>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-modern">
            <div class="alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="alert-content">
                <h6>Error</h6>
                <p>{{ session('error') }}</p>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success alert-modern">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="alert-content">
                <h6>Success</h6>
                <p>{{ session('success') }}</p>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-header">
            <h3><i class="fas fa-filter"></i> Filter & Search</h3>
            <button class="btn btn-clear" onclick="resetFilters()">
                <i class="fas fa-undo"></i> Clear Filters
            </button>
        </div>
        
        <form method="GET" class="filter-form">
            <div class="filter-grid ">
                <div class="filter-group">
                    <label><i class="fas fa-search"></i> Search</label>
                    <input type="search" 
                           id="complaintSearch" 
                           name="search" 
                           placeholder="Search by ID, description, or name..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-tag"></i> Status</label>
                    <select name="status" class="filter-select">
                        <option value="">All Statuses</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-calendar"></i> Date</label>
                    <input type="date" 
                           name="date" 
                           value="{{ request('date') }}" 
                           class="filter-date">
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-bullhorn"></i> Type</label>
                    <select name="type" class="filter-select">
                        <option value="">All Types</option>
                        <option value="Theft" {{ request('type') == 'Theft' ? 'selected' : '' }}>Theft</option>
                        <option value="Assault" {{ request('type') == 'Assault' ? 'selected' : '' }}>Assault</option>
                        <option value="Vandalism" {{ request('type') == 'Vandalism' ? 'selected' : '' }}>Vandalism</option>
                        <option value="Fraud" {{ request('type') == 'Fraud' ? 'selected' : '' }}>Fraud</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-sort"></i> Sort By</label>
                    <div class="sort-container">
                        <select name="sort_by" class="sort-select">
                            <option value="">Default</option>
                            <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>Complaint ID</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                        </select>
                        <select name="sort_order" class="order-select">
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Asc</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Desc</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
               
            </div>
        </form>
    </div>

    <!-- Complaints Table -->
    <div class="complaints-card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Complaints List</h3>
            <span class="badge badge-count">{{ $complaints->count() }} records</span>
        </div>
        
        <div class="table-container">
            <table class="complaints-table" id="complaintTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            <i class="fas fa-hashtag"></i> Tracking ID
                        </th>
                        <th>
                            <i class="fas fa-user"></i> Complaint By
                        </th>
                        <th>
                            <i class="fas fa-calendar"></i> Date
                        </th>
                        <th>
                            <i class="fas fa-tag"></i> Type
                        </th>
                        <th>
                            <i class="fas fa-circle"></i> Status
                        </th>
                        <th>
                            <i class="fas fa-user-shield"></i> Assigned To
                        </th>
                        <th class="text-center">
                            <i class="fas fa-cogs"></i> Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($complaints as $complaint)
                    <tr class="complaint-row" data-id="{{ $complaint->id }}">
                        <td class="serial">{{ $loop->iteration + ($complaints->currentPage() - 1) * $complaints->perPage() }}</td>
                        <td class="tracking-id">
                            <span class="id-badge">{{ $complaint->track_id }}</span>
                        </td>
                        <td class="complainant">
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($complaint->user?->name ?? 'N', 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $complaint->user?->name ?? 'N/A' }}</strong>
                                    <small>{{ $complaint->user?->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="date">
                            <div class="date-badge">
                                <i class="fas fa-clock"></i>
                                {{ $complaint->created_at->format('M y') }}
                            </div>
                        </td>
                        <td class="type">
                            <span class="type-badge {{ strtolower($complaint->incident_type) }}">
                                {{ $complaint->incident_type ?? '-' }}
                            </span>
                        </td>
                        <td class="status">
                            @switch($complaint->status)
                                @case('received')
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-clock"></i> Received
                                    </span>
                                    @break
                                @case('under_review')
                                    <span class="status-badge status-review">
                                        <i class="fas fa-search"></i> Under Review
                                    </span>
                                    @break
                                @case('resolved')
                                    <span class="status-badge status-resolved">
                                        <i class="fas fa-check-circle"></i> Resolved
                                    </span>
                                    @break
                                @default
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-clock"></i> {{ ucfirst($complaint->status) }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="assigned">
                            @if($complaint->assigned_to)
                                <div class="officer-info">
                                    <div class="officer-avatar">
                                        {{ strtoupper(substr($officers->firstWhere('id', $complaint->assigned_to)?->name ?? 'O', 0, 1)) }}
                                    </div>
                                    <span>{{ $officers->firstWhere('id', $complaint->assigned_to)?->name ?? 'Unknown' }}</span>
                                </div>
                            @else
                                <span class="unassigned-badge">
                                    <i class="fas fa-user-slash"></i> Unassigned
                                </span>
                            @endif
                        </td>
                        <td class="actions">
                            <div class="action-menu">
                                <button class="action-btn primary" 
                                        onclick="window.location.href='{{ route('admin.complaints.show', $complaint->id) }}'">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </button>
                                
                                <div class="dropdown">
                                    <button class="action-btn secondary dropdown-toggle" 
                                            type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <span>More</span>
                                    </button>
                                    <ul class="dropdown-menu" >
                                        <li>
                                            <a class="dropdown-item" 
                                               href="#" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#assignModal{{ $complaint->id }}">
                                                <i class="fas fa-user-check"></i> Assign Officer
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" 
                                               href="#" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#statusModal{{ $complaint->id }}">
                                                <i class="fas fa-exchange-alt"></i> Change Status
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" 
                                                  method="POST" 
                                                  class="delete-form"
                                                  onsubmit="return confirmDelete()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>

                     {{-- Assign Modal --}}
    <div class="modal fade" id="assignModal{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.complaints.assign', $complaint->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Officer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="officer_id" class="form-select" required>
                        <option value="">Select Officer</option>
                        @foreach($officers as $officer)
                            <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Status Modal --}}
    <div class="modal fade" id="statusModal{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.complaints.changeStatus', $complaint->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="received">Pending</option>
                        <option value="under_review">Under Review</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x"></i>
                                <h4>No complaints found</h4>
                                <p>Try adjusting your filters or check back later</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
    @if($complaints->hasPages())
    {{-- Pagination --}}
        <div class='d-flex justify-content-center' style="margin-top: 15px;">
            {{ $complaints->withQueryString()->links() }}
        </div>
    @endif
    </div>

    
</div>

   





       
  



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Live Search Functionality
    $("#complaintSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase().trim();
        
        if (value === "") {
            $(".complaint-row").show();
        } else {
            let found = false;
            
            $(".complaint-row").each(function() {
                let rowText = $(this).text().toLowerCase();
                if (rowText.indexOf(value) > -1) {
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });
            
            // Show/hide empty state
            if (!found && $("#emptyRow").length) {
                $("#emptyRow").show();
            } else {
                $("#emptyRow").hide();
            }
        }
    });
    
    // Row hover effects
    $(".complaint-row").hover(
        function() {
            $(this).css({
                'transform': 'translateY(-2px)',
                'box-shadow': '0 4px 12px rgba(0,0,0,0.1)'
            });
        },
        function() {
            $(this).css({
                'transform': 'translateY(0)',
                'box-shadow': 'none'
            });
        }
    );
});

function resetFilters() {
    // Clear all filter inputs
    $('.filter-form input[type="search"], .filter-form input[type="date"], .filter-form select').val('');
    // Submit the form to reload with default filters
    $('.filter-form').submit();
}

function confirmDelete() {
    return confirm('Are you sure you want to delete this complaint? This action cannot be undone.');
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert-modern').fadeOut(300, function() {
        $(this).remove();
    });
}, 5000);
</script>
@endsection

