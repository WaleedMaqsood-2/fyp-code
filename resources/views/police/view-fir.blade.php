<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage FIRs - Police Module</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
  background: #f5f7fa;
}
.page-header {
  border-bottom: 2px solid #dee2e6;
  margin-bottom: 1.5rem;
}
.status-badge {
  font-size: 0.85rem;
}
.action-btn {
  border: none;
  background: none;
  cursor: pointer;
}
.action-btn i {
  font-size: 1.2rem;
}
.action-btn:hover {
  color: #0d6efd;
}
</style>
</head>
<body>

<div class="container py-4">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h2 class="fw-bold text-primary">Manage FIRs / Cases</h2>
      <p class="text-muted mb-0">View, update, and forward filed FIRs to forensic analysts.</p>
    </div>
    <button class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> File New FIR
    </button>
  </div>

  <!-- Filters -->
  <div class="row mb-3 g-2">
    <div class="col-md-4">
      <input type="text" class="form-control" id="searchFIR" placeholder="Search by title, ID, or complainant...">
    </div>
    <div class="col-md-3">
      <select class="form-select" id="filterStatus">
        <option value="">Filter by Status</option>
        <option>Pending</option>
        <option>Under Review</option>
        <option>Forwarded</option>
        <option>Closed</option>
      </select>
    </div>
  </div>

  <!-- FIR Table -->
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-primary">
        <tr>
          <th>#</th>
          <th>Title</th>
          <th>Category</th>
          <th>Complainant</th>
          <th>Status</th>
          <th>Date Filed</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody id="firTableBody">
        <!-- Example Row -->
        <tr>
          <td>FIR-101</td>
          <td>Theft at Main Bazaar</td>
          <td>Theft</td>
          <td>Ali Raza</td>
          <td><span class="badge bg-warning text-dark status-badge">Pending</span></td>
          <td>2025-10-05</td>
          <td class="text-center">
            <button class="action-btn me-2" title="View Details" onclick="viewDetails('FIR-101')"><i class="bi bi-eye-fill"></i></button>
            <button class="action-btn me-2" title="Update Status" onclick="updateStatus('FIR-101')"><i class="bi bi-pencil-square"></i></button>
            <button class="action-btn me-2" title="Forward Case" onclick="forwardCase('FIR-101')"><i class="bi bi-send-fill"></i></button>
            <button class="action-btn text-danger" title="Delete FIR" onclick="deleteFIR('FIR-101')"><i class="bi bi-trash-fill"></i></button>
          </td>
        </tr>
        <tr>
          <td>FIR-102</td>
          <td>Online Fraud via Fake Website</td>
          <td>Cybercrime</td>
          <td>Anonymous</td>
          <td><span class="badge bg-info text-dark status-badge">Under Review</span></td>
          <td>2025-10-06</td>
          <td class="text-center">
            <button class="action-btn me-2" title="View Details" onclick="viewDetails('FIR-102')"><i class="bi bi-eye-fill"></i></button>
            <button class="action-btn me-2" title="Update Status" onclick="updateStatus('FIR-102')"><i class="bi bi-pencil-square"></i></button>
            <button class="action-btn me-2" title="Forward Case" onclick="forwardCase('FIR-102')"><i class="bi bi-send-fill"></i></button>
            <button class="action-btn text-danger" title="Delete FIR" onclick="deleteFIR('FIR-102')"><i class="bi bi-trash-fill"></i></button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">FIR Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>FIR ID:</strong> <span id="detailId"></span></p>
        <p><strong>Title:</strong> <span id="detailTitle"></span></p>
        <p><strong>Category:</strong> <span id="detailCategory"></span></p>
        <p><strong>Description:</strong> <span id="detailDesc"></span></p>
        <p><strong>Status:</strong> <span class="badge bg-info" id="detailStatus"></span></p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-success">Forward to Forensic Analyst</button>
      </div>
    </div>
  </div>
</div>

<script>
function viewDetails(id) {
  // Example static data (later connect to backend)
  document.getElementById('detailId').innerText = id;
  document.getElementById('detailTitle').innerText = "Theft at Main Bazaar";
  document.getElementById('detailCategory').innerText = "Theft";
  document.getElementById('detailDesc').innerText = "Reported theft case with missing jewelry and phone.";
  document.getElementById('detailStatus').innerText = "Pending";

  const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
  modal.show();
}

function updateStatus(id) {
  Swal.fire({
    title: 'Update FIR Status',
    input: 'select',
    inputOptions: {
      'Pending': 'Pending',
      'Under Review': 'Under Review',
      'Forwarded': 'Forwarded',
      'Closed': 'Closed'
    },
    inputPlaceholder: 'Select new status',
    showCancelButton: true
  }).then(result => {
    if (result.isConfirmed) {
      Swal.fire('Updated!', 'Status changed to ' + result.value, 'success');
    }
  });
}

function forwardCase(id) {
  Swal.fire({
    title: 'Forward to Forensic Analyst?',
    text: 'This will notify the forensic team for analysis.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Forward'
  }).then(result => {
    if (result.isConfirmed) {
      Swal.fire('Forwarded!', 'Case ' + id + ' sent to Forensic Analyst.', 'success');
    }
  });
}

function deleteFIR(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This FIR will be permanently deleted.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Delete'
  }).then(result => {
    if (result.isConfirmed) {
      Swal.fire('Deleted!', 'FIR ' + id + ' removed from records.', 'success');
    }
  });
}
</script>
</body>
</html>
