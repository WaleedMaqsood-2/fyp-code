<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forward Case to Forensic Analyst - Police Module</title>
<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
body {
  background-color: #f5f7fa;
}
.card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.table thead {
  background: #0d6efd;
  color: #fff;
}
.modal-content {
  border-radius: 12px;
}
.status-badge {
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.85rem;
}
.status-pending {
  background: #fff3cd;
  color: #856404;
}
.status-progress {
  background: #cfe2ff;
  color: #084298;
}
.status-complete {
  background: #d1e7dd;
  color: #0f5132;
}
</style>
</head>
<body>

<div class="container py-5">
  <h2 class="fw-bold text-primary mb-4">Forward Case to Forensic Analyst</h2>

  <!-- Case Selection -->
  <div class="card mb-5">
    <div class="card-body">
      <h5 class="mb-3 text-primary"><span class="material-icons align-middle">folder_shared</span> Select Case to Forward</h5>
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Case ID</th>
            <th>Title</th>
            <th>Officer</th>
            <th>Date Filed</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#P-103</td>
            <td>Burglary at Main Street</td>
            <td>Officer Ali</td>
            <td>08 Oct 2025</td>
            <td><span class="status-badge status-pending">Pending</span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#forwardModal">
                <span class="material-icons fs-6">send</span> Forward
              </button>
            </td>
          </tr>
          <tr>
            <td>#P-104</td>
            <td>Cyber Fraud Case</td>
            <td>Officer Sara</td>
            <td>07 Oct 2025</td>
            <td><span class="status-badge status-progress">In Progress</span></td>
            <td>
              <button class="btn btn-sm btn-outline-secondary" disabled>
                <span class="material-icons fs-6">check_circle</span> Sent
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Forward Tracking -->
  <div class="card">
    <div class="card-body">
      <h5 class="mb-3 text-primary"><span class="material-icons align-middle">track_changes</span> Forwarded Cases Tracking</h5>
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Case ID</th>
            <th>Forensic Analyst</th>
            <th>Forwarded On</th>
            <th>Remarks</th>
            <th>Analysis Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#P-100</td>
            <td>Analyst Hamza</td>
            <td>02 Oct 2025</td>
            <td>Need deep face match verification</td>
            <td><span class="status-badge status-complete">Completed</span></td>
          </tr>
          <tr>
            <td>#P-102</td>
            <td>Analyst Ayesha</td>
            <td>05 Oct 2025</td>
            <td>Verify voice match and digital evidence</td>
            <td><span class="status-badge status-progress">In Progress</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Forward Modal -->
<div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="forwardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="forwardModalLabel"><span class="material-icons align-middle">send</span> Forward Case</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label class="form-label">Select Forensic Analyst</label>
            <select class="form-select">
              <option>Hamza Khan</option>
              <option>Ayesha Ahmed</option>
              <option>Bilal Qureshi</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Add Remarks / Instructions</label>
            <textarea class="form-control" rows="3" placeholder="Describe what to analyze..."></textarea>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-success">
              <span class="material-icons">check_circle</span> Confirm Forward
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
