@extends('police.layouts.main')
@section('title', 'Complaint Management - Police Module')
<style>
body {
  background-color: #f5f7fa;
}
.card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.status-badge {
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.85rem;
}
.status-pending { background: #fff3cd; color: #856404; }
.status-investigating { background: #cfe2ff; color: #084298; }
.status-resolved { background: #d1e7dd; color: #0f5132; }
audio {
  width: 100%;
  outline: none;
}
</style>
@section('content')
<div class="container py-5">
  <h2 class="fw-bold text-primary mb-4">Public Complaints Inbox</h2>

  <!-- Filter and Search -->
  <div class="card mb-4">
    <div class="card-body">
      <form class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label">Filter by Status</label>
          <select class="form-select">
            <option>All</option>
            <option>Pending</option>
            <option>Investigating</option>
            <option>Resolved</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Date Range</label>
          <input type="date" class="form-control">
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Search complaint...">
        </div>
        <div class="col-md-1">
          <button class="btn btn-primary w-100"><span class="material-icons">search</span></button>
        </div>
      </form>
    </div>
  </div>

  <!-- Complaints Table -->
  <div class="card">
    <div class="card-body">
      <h5 class="text-primary mb-3"><span class="material-icons align-middle">report_problem</span> Public Complaints</h5>

      <table class="table table-bordered align-middle">
        <thead class="table-primary">
          <tr>
            <th>Complaint ID</th>
            <th>Type</th>
            <th>Submitted By</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#C-210</td>
            <td>Audio Complaint</td>
            <td>Anonymous</td>
            <td>08 Oct 2025</td>
            <td><span class="status-badge status-pending">Pending</span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewComplaintModal">
                <span class="material-icons fs-6">visibility</span>
              </button>
              <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignModal">
                <span class="material-icons fs-6">assignment_ind</span>
              </button>
            </td>
          </tr>
          <tr>
            <td>#C-211</td>
            <td>Written Complaint</td>
            <td>Ahmed Khan</td>
            <td>07 Oct 2025</td>
            <td><span class="status-badge status-investigating">Investigating</span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewComplaintModal">
                <span class="material-icons fs-6">visibility</span>
              </button>
              <button class="btn btn-sm btn-outline-secondary" disabled>
                <span class="material-icons fs-6">done</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- View Complaint Modal -->
<div class="modal fade" id="viewComplaintModal" tabindex="-1" aria-labelledby="viewComplaintModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="viewComplaintModalLabel"><span class="material-icons align-middle">visibility</span> View Complaint</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6>Complaint ID: #C-210</h6>
        <p><strong>Type:</strong> Audio Complaint</p>
        <p><strong>Submitted By:</strong> Anonymous</p>
        <p><strong>Date:</strong> 08 Oct 2025</p>

        <div class="mb-3">
          <label class="form-label">Audio Evidence</label>
          <audio controls>
            <source src="{{ asset('assets/audio/sample-complaint.mp3') }}" type="audio/mp3">
          </audio>
        </div>

        <div class="mb-3">
          <label class="form-label">AI Transcription</label>
          <textarea class="form-control" rows="4" readonly>This is an automatically generated transcription of the submitted audio complaint...</textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Police Notes</label>
          <textarea class="form-control" rows="3" placeholder="Add internal notes or actions..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success"><span class="material-icons">save</span> Save Notes</button>
      </div>
    </div>
  </div>
</div>

<!-- Assign Officer Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="assignModalLabel"><span class="material-icons align-middle">assignment_ind</span> Assign Complaint</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label class="form-label">Select Officer</label>
            <select class="form-select">
              <option>Officer Ali</option>
              <option>Officer Sara</option>
              <option>Officer Hamid</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" rows="3" placeholder="Any specific instructions..."></textarea>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-success">
              <span class="material-icons">send</span> Assign
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
