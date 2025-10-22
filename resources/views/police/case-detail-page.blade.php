<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Case Details - Police Module</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body {
  background: #f5f7fa;
}
.header-section {
  background: linear-gradient(90deg, #0d6efd, #0dcaf0);
  color: white;
  border-radius: 10px;
  padding: 1.5rem;
  margin-bottom: 2rem;
}
.case-info {
  background: white;
  border-radius: 10px;
  padding: 1rem;
  box-shadow: 0 0 8px rgba(0,0,0,0.1);
}
.tab-content {
  background: white;
  border-radius: 10px;
  padding: 1.5rem;
  box-shadow: 0 0 8px rgba(0,0,0,0.1);
}
.timeline {
  position: relative;
  border-left: 3px solid #0d6efd;
  padding-left: 1.5rem;
}
.timeline-item {
  margin-bottom: 1.5rem;
}
.timeline-item::before {
  content: '';
  position: absolute;
  left: -9px;
  background-color: #0d6efd;
  border-radius: 50%;
  width: 14px;
  height: 14px;
}
.evidence-item {
  border: 1px solid #dee2e6;
  border-radius: 10px;
  padding: .75rem;
  text-align: center;
  transition: all 0.3s;
}
.evidence-item:hover {
  transform: scale(1.03);
}
</style>
</head>

<body>
<div class="container py-4">

  <!-- Header Section -->
  <div class="header-section d-flex justify-content-between align-items-center">
    <div>
      <h3 class="fw-bold mb-1">Case: Theft at Main Bazaar</h3>
      <p class="mb-0">FIR ID: <strong>#FIR-101</strong> | Status: <span class="badge bg-warning text-dark">Pending</span></p>
    </div>
    <div>
      <button class="btn btn-light me-2" onclick="updateStatus()"><i class="bi bi-pencil-square"></i> Update Status</button>
      <button class="btn btn-success me-2" onclick="forwardToForensic()"><i class="bi bi-send-fill"></i> Forward</button>
      <button class="btn btn-outline-light" onclick="exportPDF()"><i class="bi bi-file-earmark-pdf-fill"></i> Export PDF</button>
    </div>
  </div>

  <!-- Case Info Summary -->
  <div class="case-info mb-4">
    <div class="row g-3">
      <div class="col-md-4"><strong>Filed By:</strong> Ali Raza</div>
      <div class="col-md-4"><strong>Category:</strong> Theft</div>
      <div class="col-md-4"><strong>Date Filed:</strong> 2025-10-05</div>
      <div class="col-md-12"><strong>Location:</strong> Main Bazaar, Lahore</div>
    </div>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="caseTabs">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview">Overview</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#evidence">Evidence</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#audio">Audio</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#timeline">Timeline</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#notes">Notes</button></li>
  </ul>

  <div class="tab-content">

    <!-- Overview Tab -->
    <div class="tab-pane fade show active" id="overview">
      <h5 class="fw-bold">Incident Description</h5>
      <p>Complainant reported theft of jewelry and a mobile phone from residence in Main Bazaar around midnight. Suspect escaped before police arrival.</p>

      <h6 class="fw-bold mt-4">AI Generated Summary</h6>
      <div class="alert alert-secondary">
        <i class="bi bi-robot"></i> “AI suggests the crime occurred between 11 PM–12 AM, likely involving a single intruder targeting high-value items.”
      </div>
    </div>

    <!-- Evidence Tab -->
    <div class="tab-pane fade" id="evidence">
      <div class="row g-3">
        <div class="col-md-3">
          <div class="evidence-item">
            <img src="https://via.placeholder.com/120" class="img-fluid rounded mb-2" alt="Evidence">
            <p class="small mb-1">CCTV Capture 1</p>
            <a href="#" class="text-decoration-none small"><i class="bi bi-eye"></i> View</a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="evidence-item">
            <video width="100%" height="120" controls>
              <source src="sample.mp4" type="video/mp4">
            </video>
            <p class="small mb-1">Surveillance Clip</p>
            <a href="#" class="text-decoration-none small"><i class="bi bi-download"></i> Download</a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="evidence-item">
            <i class="bi bi-file-earmark-text fs-1 text-secondary"></i>
            <p class="small mb-1">Report.pdf</p>
            <a href="#" class="text-decoration-none small"><i class="bi bi-download"></i> Download</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Audio Tab -->
    <div class="tab-pane fade" id="audio">
      <p><strong>Voice Complaint Recording:</strong></p>
      <audio controls class="w-100 mb-3">
        <source src="complaint_audio.mp3" type="audio/mpeg">
      </audio>

      <p><strong>Forensic Processed Clip:</strong></p>
      <audio controls class="w-100">
        <source src="forensic_clip.mp3" type="audio/mpeg">
      </audio>
    </div>

    <!-- Timeline Tab -->
    <div class="tab-pane fade" id="timeline">
      <div class="timeline">
        <div class="timeline-item">
          <p class="mb-1 fw-bold">2025-10-05 - FIR Filed</p>
          <p class="small text-muted">Complaint registered by Ali Raza at Police Station #12.</p>
        </div>
        <div class="timeline-item">
          <p class="mb-1 fw-bold">2025-10-06 - Officer Assigned</p>
          <p class="small text-muted">Case assigned to Inspector Asad Khan for investigation.</p>
        </div>
        <div class="timeline-item">
          <p class="mb-1 fw-bold">2025-10-07 - Evidence Collected</p>
          <p class="small text-muted">CCTV footage and fingerprint scans gathered from the scene.</p>
        </div>
      </div>
    </div>

    <!-- Notes Tab -->
    <div class="tab-pane fade" id="notes">
      <div class="mb-3">
        <label class="form-label fw-semibold">Add Internal Note</label>
        <textarea class="form-control" rows="3" placeholder="Write your note here..."></textarea>
      </div>
      <button class="btn btn-primary mb-3"><i class="bi bi-save"></i> Save Note</button>
      <hr>
      <h6 class="fw-bold">Recent Notes</h6>
      <ul class="list-group">
        <li class="list-group-item small"><strong>Inspector Asad:</strong> Visited crime scene, collected footage (2025-10-06)</li>
        <li class="list-group-item small"><strong>Analyst Team:</strong> AI summary pending verification (2025-10-07)</li>
      </ul>
    </div>

  </div>
</div>

<script>
function updateStatus() {
  Swal.fire({
    title: 'Update Case Status',
    input: 'select',
    inputOptions: {
      'Pending': 'Pending',
      'Under Review': 'Under Review',
      'Forwarded': 'Forwarded',
      'Closed': 'Closed'
    },
    inputPlaceholder: 'Select status',
    showCancelButton: true
  }).then(result => {
    if (result.isConfirmed) Swal.fire('Updated!', 'Case status set to ' + result.value, 'success');
  });
}

function forwardToForensic() {
  Swal.fire({
    title: 'Forward to Forensic Analyst?',
    text: 'This will notify the forensic department.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Forward'
  }).then(result => {
    if (result.isConfirmed) Swal.fire('Forwarded!', 'Case sent successfully.', 'success');
  });
}

function exportPDF() {
  Swal.fire('Exported!', 'PDF report generated successfully.', 'success');
}
</script>
</body>
</html>
