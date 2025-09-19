@extends('layouts.master')

@section('content')
<div class="container">
    <div class="ms-2 mt-4">
     @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        
    @endif
    @if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif

        <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">Complaint Management</h1>
        <p style="color: #6b7280; margin-bottom: 20px;">View, filter, and manage all submitted complaints.</p>

        {{-- Filters --}}
        <div style="margin-bottom: 20px; padding: 15px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; display: flex; flex-wrap: wrap; gap: 15px;">
            <form method="GET" style="display: flex; flex-wrap: wrap; gap: 15px; width: 100%;">

                <input type="search" id="complaintSearch" name="search" placeholder="ID, description..." 
    style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; flex: 1; min-width: 150px;">

                <select name="status" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                    <option value="">All Statuses</option>
                    <option value="received" {{ request('status')=='received' ? 'selected' : '' }}>Received</option>
                    <option value="under_review" {{ request('status')=='under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="resolved" {{ request('status')=='resolved' ? 'selected' : '' }}>Resolved</option>
                </select>

                <input type="date" name="date" value="{{ request('date') }}" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px;">

                <select name="type" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                    <option value="">All Types</option>
                    <option value="Theft" {{ request('type')=='Theft' ? 'selected' : '' }}>Theft</option>
                    <option value="Assault" {{ request('type')=='Assault' ? 'selected' : '' }}>Assault</option>
                    <option value="Vandalism" {{ request('type')=='Vandalism' ? 'selected' : '' }}>Vandalism</option>
                    <option value="Fraud" {{ request('type')=='Fraud' ? 'selected' : '' }}>Fraud</option>
                </select>

                {{-- Sorting --}}
                <select name="sort_by" style="padding:6px 10px; border:1px solid #d1d5db; border-radius:6px;">
                    <option value="">Sort By</option>
                    <option value="id" {{ request('sort_by')=='id' ? 'selected' : '' }}>Complaint ID</option>
                    <option value="created_at" {{ request('sort_by')=='created_at' ? 'selected' : '' }}>Date</option>
                </select>

                <select name="sort_order" style="padding:6px 10px; border:1px solid #d1d5db; border-radius:6px;">
                    <option value="asc" {{ request('sort_order')=='asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('sort_order')=='desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <button type="submit" style="padding: 6px 12px; background: #0ea5e9; color: #fff; border-radius: 6px; border: none;">Filter</button>
            </form>
        </div>

        {{-- Complaints Table --}}
        <div style="overflow-x: auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse;" id="complaintTable">
                <thead style="background: #f9fafb;">
                    <tr>
                        <th style="padding: 10px; border-bottom: 1px solid #e5e7eb;">#</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left;">Tracking ID</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left;">Complaint By</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left;">Date</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left;">Complainant</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left;">Type</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left;">Status</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left;">Assigned To</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($complaints as $complaint)
                    <tr style="background: {{ $loop->even ? '#f9fafb' : '#fff' }};">
                        <td style="padding: 10px;">{{ $loop->iteration }}</td>
                        <td style="padding: 10px;">{{ $complaint->track_id }}</td>
                        <td style="padding: 10px;">{{ $complaint->user?->name ?? 'N/A' }}</td>
                        <td style="padding: 10px;">{{ $complaint->created_at->format('Y-m-d') }}</td>
                        <td style="padding: 10px;">{{ $complaint->user?->name ?? 'N/A' }}</td>
                        <td style="padding: 10px;">{{ $complaint->incident_type ?? '-' }}</td>
                        <td style="padding: 10px;">
                            <span style="padding: 2px 6px; border-radius: 12px; font-size: 12px; color: #fff; 
                                background: 
                                    @switch($complaint->status)
                                        @case('received') #facc15 @break
                                        @case('under_review') #3b82f6 @break
                                        @case('resolved') #22c55e @break
                                        @default #6b7280
                                    @endswitch;">
                                {{ ucfirst(str_replace('_',' ',$complaint->status)) }}
                            </span>
                        </td>
                        <td style="padding: 10px;">{{ $complaint->assigned_to ? $officers->firstWhere('id', $complaint->assigned_to)?->name : 'Unassigned' }}</td>
                        
<td style="padding: 10px; text-align: right;">
    <div class="dropdown position-static">
        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Actions
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('admin.complaints.show', $complaint->id) }}">
                    👁 View
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#assignModal{{ $complaint->id }}">
                    👮 Assign Officer
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#statusModal{{ $complaint->id }}">
                    ⚡ Change Status
                </a>
            </li>
            <li>
                <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this complaint?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">
                        🗑 Delete
                    </button>
                </form>
            </li>
        </ul>
    </div>

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

</td>




                    </tr>
                
                  
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 10px; text-align: center;">No complaints found.</td>
                    </tr>
                    @endforelse
                      <tr id="noComplaintRow" style="display: none;">
                        <td colspan="9" class="text-center text-danger fw-bold ">No complaint found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class='d-flex justify-content-center' style="margin-top: 15px;">
            {{ $complaints->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
$(document).ready(function() {
    $("#complaintSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase().trim();

        if (value === "") {
            // Agar input empty hai to sab rows show kar do
            $("#complaintTable tbody tr").show();
            $("#noComplaintRow").hide(); // no complaint wali row hide rahegi
        } else {
            let found = false;

            $("#complaintTable tbody tr").not("#noComplaintRow").each(function() {
                let rowText = $(this).text().toLowerCase();
                if (rowText.indexOf(value) > -1) {
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });

            // Agar koi match na mila to "No complaint found" row show karo
            if (!found) {
                $("#noComplaintRow").show();
            } else {
                $("#noComplaintRow").hide();
            }
        }
    });
});
</script>




<script>
function handleComplaintAction(select, complaintId) {
    let action = select.value;

    // hide sub dropdowns initially
    document.getElementById("assignOfficer" + complaintId).style.display = "none";
    document.getElementById("changeStatus" + complaintId).style.display = "none";

    if (action === "view") {
        window.location.href = "/admin/complaints/" + complaintId;
    } 
    else if (action === "assign") {
        document.getElementById("assignOfficer" + complaintId).style.display = "inline-block";
    } 
    else if (action === "status") {
        document.getElementById("changeStatus" + complaintId).style.display = "inline-block";
    } 
    else if (action === "delete") {
        if (confirm("Are you sure you want to delete this complaint?")) {
            let form = document.getElementById("complaintActionForm" + complaintId);
            form.action = "/admin/complaints/" + complaintId;
            form.method = "POST";
            form.innerHTML += `@method('DELETE')`;
            form.submit();
        }
    }

    select.value = ""; // reset dropdown
}

function submitAssign(complaintId) {
    let form = document.getElementById("complaintActionForm" + complaintId);
    form.action = "/admin/complaints/" + complaintId + "/assign";
    form.submit();
}

function submitStatus(complaintId) {
    let form = document.getElementById("complaintActionForm" + complaintId);
    form.action = "/admin/complaints/" + complaintId + "/change-status";
    form.submit();
}
</script>
