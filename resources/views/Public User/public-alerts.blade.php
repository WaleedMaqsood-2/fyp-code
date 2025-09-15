<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CrimeTrack - Public Alerts</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Eczar:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Material Symbols -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

  <style>
    :root {
      --primary-color: #1173d4;
    }
    body {
      background-color: #f9fafb;
      font-family: "Public Sans", sans-serif;
    }
    .navbar-brand svg {
      color: var(--primary-color);
    }
    .navbar-brand h2 {
      font-family: "Eczar", serif;
      font-weight: 700;
    }
    .nav-link.active, 
    .nav-link:hover {
      color: var(--primary-color) !important;
      font-weight: 600;
    }
    .form-control:focus, 
    .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.25rem rgba(17,115,212,0.25);
    }
    .badge-category {
      font-size: 0.9rem;
      padding: 0.4em 0.8em;
      border-radius: 50rem;
      font-weight: 600;
    }
  </style>
</head>
<body class="text-dark">
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
<header class="border-bottom bg-white shadow-sm py-3">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 48 48">
        <path d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z"/>
      </svg>
      <h2 class="mb-0">CrimeTrack</h2>
    </div>
    <nav class="d-flex gap-4">
      <a class="nav-link" href="#">Home</a>
      <a class="nav-link" href="#">Report Incident</a>
      <a class="nav-link" href="#">Track Complaint</a>
      <a class="nav-link active" href="#">Public Alerts</a>
      <a class="nav-link" href="#">Resources</a>
    </nav>
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-light position-relative rounded-circle p-2">
        <span class="material-symbols-outlined">notifications</span>
        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
      </button>
      <div class="rounded-circle" style="width:40px; height:40px; background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuDi7L5e9bK6Hg26U_LzhJaEwSqFaKMwmc7muRp0lM1PUpMLXwYFd-N-2HEn1i_NO0Z-IF-XOIvpfzHJRqjS_C0JpZYz1NzGhsAzX0AWTukYWMRQTr16A9Bhwv1acWfrzVeqbNujxqzHxFVIkd4Fjnh7LVhUMboNvwxeL4gECjtWQ75Ch2xkIcD5A-E4oYBqnTEdsMINQFFMxmB05kIPwKIm5bUhzG4Tbj9XcfEv_3MmMbVP8pFUO6sRmrX8Sit8gnEzVCNRFhh5AfdE'); background-size:cover; background-position:center;"></div>
    </div>
  </div>
</header>

<!-- Main -->
<main class="container py-5">
  <div class="text-center mb-5">
    <h1 class="fw-bold" style="font-family: 'Eczar', serif;">Public Alerts</h1>
    <p class="text-muted">Stay informed about recent incidents and safety warnings in your area.</p>
  </div>

  <!-- Search + Filters -->
  <div class="card shadow-sm mb-5">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-12">
          <label for="search" class="form-label">Search Alerts</label>
          <div class="input-group">
            <span class="input-group-text bg-white"><span class="material-symbols-outlined text-muted">search</span></span>
            <input type="text" class="form-control" id="search" placeholder="Search by keyword, location, etc.">
          </div>
        </div>
      </div>

      <form class="row g-3 mt-4">
        <div class="col-sm-6 col-md-3">
          <label for="alert-type" class="form-label">Alert Type</label>
          <select id="alert-type" class="form-select ">
            <option>All Types</option>
            <option>Crime</option>
            <option>Safety</option>
            <option>Missing Person</option>
            <option>Traffic</option>
          </select>
        </div>
        <div class="col-sm-6 col-md-3">
          <label for="location" class="form-label">Location</label>
          <input type="text" class="form-control " id="location" placeholder="Enter city or zip code">
        </div>
        <div class="col-sm-6 col-md-3">
          <label for="date-range" class="form-label">Date</label>
          <input type="date" class="form-control " id="date-range">
        </div>
        <div class="col-sm-6 col-md-3 d-flex align-items-end">
          <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center">
            <span class="material-symbols-outlined me-2">filter_list</span> Filter
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Alerts List -->
  <div class="space-y-4">
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h2 class="h5 fw-bold" style="font-family: 'Eczar', serif;">Theft Alert: Rash of Car Break-ins</h2>
            <p class="text-muted small mb-0">Posted on July 20, 2024 | Location: Downtown Anytown</p>
          </div>
          <span class="badge-category bg-danger-subtle text-danger">Crime</span>
        </div>
        <p class="mt-3 mb-0">Police are advising residents to be vigilant after a series of car break-ins were reported in the downtown area. Please ensure your vehicles are locked and valuables are not left in plain sight. Anyone with information is urged to contact the Anytown Police Department.</p>
      </div>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h2 class="h5 fw-bold" style="font-family: 'Eczar', serif;">Safety Warning: Severe Weather Expected</h2>
            <p class="text-muted small mb-0">Posted on July 19, 2024 | Location: Anytown and surrounding areas</p>
          </div>
          <span class="badge-category bg-warning-subtle text-warning">Safety</span>
        </div>
        <p class="mt-3 mb-0">The National Weather Service has issued a severe thunderstorm warning for Anytown. Expect heavy rain, strong winds, and potential hail. Please take necessary precautions and stay indoors if possible. Monitor local news for updates.</p>
      </div>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h2 class="h5 fw-bold" style="font-family: 'Eczar', serif;">Missing Person: John Doe</h2>
            <p class="text-muted small mb-0">Posted on July 18, 2024 | Location: North Anytown</p>
          </div>
          <span class="badge-category bg-primary-subtle text-primary">Missing Person</span>
        </div>
        <p class="mt-3 mb-0">John Doe, 72, was last seen near his home in North Anytown on July 17. He is 5'10", has gray hair, and was wearing a blue jacket. He suffers from dementia. If you have any information, please contact the authorities immediately.</p>
      </div>
    </div>
  </div>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
