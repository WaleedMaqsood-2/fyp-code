<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CrimeTrack - Complaint Tracking</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Eczar:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Material Symbols -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

  <style>
    body {
      background-color: #f9fafb;
      font-family: "Public Sans", sans-serif;
      color: #333;
    }
    .navbar-brand svg {
      color: #1173d4;
    }
    .navbar-brand h2 {
      font-family: "Eczar", serif;
      font-weight: 700;
      margin: 0;
    }
    .nav-link.active {
      color: #1173d4 !important;
      font-weight: 600;
    }
    .btn-primary {
      background-color: #1173d4;
      border-color: #1173d4;
    }
    .btn-primary:hover {
      background-color: #0e5cad;
    }
    .timeline {
      position: relative;
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .timeline::before {
      content: "";
      position: absolute;
      top: 0;
      left: 20px;
      width: 2px;
      height: 100%;
      background: #e5e7eb;
    }
    .timeline-item {
      position: relative;
      margin-bottom: 2rem;
      padding-left: 3.5rem;
    }
    .timeline-icon {
      position: absolute;
      top: 0;
      left: 0;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #1173d4;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .timeline-icon.gray {
      background: #9ca3af;
    }
  </style>
</head>
<body>
  @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif
<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm py-3">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="#">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 48 48">
        <path d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z"/>
      </svg>
      <h2>CrimeTrack</h2>
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-3">
        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Report Incident</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Track Complaint</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Resources</a></li>
      </ul>
    </div>
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-light position-relative rounded-circle">
        <span class="material-symbols-outlined">notifications</span>
        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
      </button>
      <div class="rounded-circle" style="width:40px; height:40px; background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuDi7L5e9bK6Hg26U_LzhJaEwSqFaKMwmc7muRp0lM1PUpMLXwYFd-N-2HEn1i_NO0Z-IF-XOIvpfzHJRqjS_C0JpZYz1NzGhsAzX0AWTukYWMRQTr16A9Bhwv1acWfrzVeqbNujxqzHxFVIkd4Fjnh7LVhUMboNvwxeL4gECjtWQ75Ch2xkIcD5A-E4oYBqnTEdsMINQFFMxmB05kIPwKIm5bUhzG4Tbj9XcfEv_3MmMbVP8pFUO6sRmrX8Sit8gnEzVCNRFhh5AfdE'); background-size:cover;"></div>
    </div>
  </div>
</nav>

<!-- Main -->
<main class="container my-5 w-75 mx-auto">
  <div class="text-center mb-5">
    <h1 class="fw-bold" style="font-family: 'Eczar', serif;">Complaint Tracking</h1>
    <p class="text-muted">Track the progress of your submitted complaint using the reference number provided.</p>
  </div>

  <!-- Complaint Form -->

    <div class="card shadow-sm mb-5">
  
    <div class="card-body">
      <form class="row g-3 align-items-end">
        <div class="col-md-9">
          <label for="complaint-ref" class="form-label">Complaint Reference Number</label>
          <input type="text" id="complaint-ref" class="form-control form-control-lg" placeholder="Enter Complaint Reference Number" value="CT-2024-001234">
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-primary w-100 btn-lg">Track</button>
        </div>
      </form>
    </div>
  </div>
 

  <!-- Complaint Details -->
  <div class="card shadow-sm mb-5">
    <div class="card-body">
      <h2 class="fw-bold" style="font-family: 'Eczar', serif;">Complaint Details</h2>
      <div class="row mt-4 g-4">
        <div class="col-md-6">
          <p class="text-muted mb-1">Reference Number</p>
          <p class="fw-semibold">CT-2024-001234</p>
        </div>
        <div class="col-md-6">
          <p class="text-muted mb-1">Date Submitted</p>
          <p class="fw-semibold">July 15, 2024</p>
        </div>
        <div class="col-md-6">
          <p class="text-muted mb-1">Incident Type</p>
          <p class="fw-semibold">Theft</p>
        </div>
        <div class="col-md-6">
          <p class="text-muted mb-1">Location</p>
          <p class="fw-semibold">123 Elm Street, Anytown</p>
        </div>
        <div class="col-12">
          <p class="text-muted mb-1">Current Status</p>
          <p class="fw-bold text-success">Under Review</p>
        </div>
        <div class="col-12">
          <p class="text-muted mb-1">Summary</p>
          <p>Your complaint is currently under review. An investigator has been assigned and is gathering evidence. Please expect an update within 2 business days.</p>
        </div>
      </div>
    </div>
  </div>
 
  <!-- Progress Timeline -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h2 class="fw-bold" style="font-family: 'Eczar', serif;">Progress Timeline</h2>
      <ul class="timeline mt-4">
        <li class="timeline-item">
          <span class="timeline-icon"><span class="material-symbols-outlined">description</span></span>
          <p class="fw-bold mb-1">Complaint Submitted</p>
          <small class="text-muted">July 15, 2024</small>
        </li>
        <li class="timeline-item">
          <span class="timeline-icon"><span class="material-symbols-outlined">search</span></span>
          <p class="fw-bold mb-1">Under Review</p>
          <small class="text-muted">July 16, 2024</small>
        </li>
        <li class="timeline-item">
          <span class="timeline-icon gray"><span class="material-symbols-outlined">person</span></span>
          <p class="text-muted mb-1">Investigation Assigned</p>
          <small class="text-muted">July 17, 2024 (Upcoming)</small>
        </li>
        <li class="timeline-item">
          <span class="timeline-icon gray"><span class="material-symbols-outlined">hourglass_empty</span></span>
          <p class="text-muted mb-1">Resolution Pending</p>
          <small class="text-muted">July 18, 2024 (Upcoming)</small>
        </li>
      </ul>
    </div>
  </div>

</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
