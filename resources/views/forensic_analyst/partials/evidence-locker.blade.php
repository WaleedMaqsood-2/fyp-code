<!-- Next Screen: Evidence Locker (Standalone Page) -->
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Evidence Locker - Forensic Analyst</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{
      --primary: #1173d4;
      --accent: #ff6600;
      --bg-light: #f6f7f8;
      --bg-dark: #101922;
      --card-dark: #111a22;
    }

    [data-bs-theme="light"]{
      --bs-body-bg: var(--bg-light);
      --bs-body-color: #212529;
      --card-bg: #ffffff;
      --bs-border: #dee2e6;
    }

    [data-bs-theme="dark"]{
      --bs-body-bg: var(--bg-dark);
      --bs-body-color: #e9ecef;
      --card-bg: var(--card-dark);
      --bs-border: #233648;
    }

    body{
      background:var(--bs-body-bg);
      color:var(--bs-body-color);
      font-family: "Public Sans", sans-serif;
    }

    header{
      height:64px;
      background:var(--card-bg);
      border-bottom:1px solid var(--bs-border);
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:0 1rem;
      position:fixed;
      top:0; left:0; right:0;
      z-index:1000;
    }

    main{
      padding:90px 1.5rem 2rem;
    }

    .evidence-card{
      background:var(--card-bg);
      border:1px solid var(--bs-border);
      border-radius:.75rem;
      padding:1rem;
      transition:0.2s ease;
      cursor:pointer;
    }

    .evidence-card:hover{
      border-color:var(--primary);
      box-shadow:0 3px 10px rgba(0,0,0,0.1);
    }

    .preview-box{
      width:100%;
      height:170px;
      background:#000;
      border-radius:.5rem;
      overflow:hidden;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .preview-box img{
      width:100%;
      height:100%;
      object-fit:cover;
      opacity:.85;
    }

    .badge-file{
      background:var(--primary);
      color:#fff;
    }
  </style>
</head>
<body>

<header>
  <h5 class="fw-bold mb-0"><i class="bi bi-box-seam"></i> Evidence Locker</h5>
  <div>
    <button class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i> Back</button>
    <button class="btn btn-sm btn-primary"><i class="bi bi-upload"></i> Upload Evidence</button>
  </div>
</header>

<main class="container-fluid">

  <h3 class="fw-bold">Evidence Overview</h3>
  <p class="text-muted">All submitted media & documents for case <strong>FIR-2023-08-1123</strong></p>

  <div class="row g-3 mt-3">

    <!-- Evidence Item 1 -->
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="evidence-card">
        <div class="preview-box mb-2">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Security_camera.png/640px-Security_camera.png" alt="Video Evidence">
        </div>
        <h6 class="fw-semibold">CCTV_Footage_Lobby_1.mp4</h6>
        <span class="badge badge-file">Video</span>
        <p class="small text-muted mt-2">Uploaded: 2 hours ago</p>
        <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-eye"></i> View Evidence</button>
      </div>
    </div>

    <!-- Evidence Item 2 -->
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="evidence-card">
        <div class="preview-box mb-2 bg-dark text-white d-flex align-items-center justify-content-center">
          <i class="bi bi-mic fs-1"></i>
        </div>
        <h6 class="fw-semibold">Robbery_Threat_Audio.wav</h6>
        <span class="badge bg-warning text-dark">Audio</span>
        <p class="small text-muted mt-2">Uploaded: 5 hours ago</p>
        <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-eye"></i> Listen</button>
      </div>
    </div>

    <!-- Evidence Item 3 -->
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="evidence-card">
        <div class="preview-box mb-2 bg-secondary d-flex align-items-center justify-content-center text-white">
          <i class="bi bi-file-earmark-text fs-1"></i>
        </div>
        <h6 class="fw-semibold">Witness_Statement.pdf</h6>
        <span class="badge bg-danger">Document</span>
        <p class="small text-muted mt-2">Uploaded: 1 day ago</p>
        <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-eye"></i> Read Document</button>
      </div>
    </div>

  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
