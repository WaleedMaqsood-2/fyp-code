{{-- <!doctype html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Forensic Analyst Dashboard</title>

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root{
      --primary: #1173d4;
      --accent: #ff6600;
      --bg-light: #f6f7f8;
      --bg-dark: #101922;
      --card-dark: #111a22;
      --status-high: #D32F2F;
      --status-medium: #FFA000;
      --status-completed: #388E3C;
      --status-info: #1976D2;
    }

    [data-bs-theme="light"]{
      --bs-body-bg: var(--bg-light);
      --bs-body-color: #212529;
      --bs-border: #dee2e6;
      --card-bg: #ffffff;
      --nav-bg: #ffffff;
      --sidebar-bg: #ffffff;
      --muted: #6c757d;
    }
    [data-bs-theme="dark"]{
      --bs-body-bg: var(--bg-dark);
      --bs-body-color: #e9ecef;
      --bs-border: #233648;
      --card-bg: var(--card-dark);
      --nav-bg: var(--card-dark);
      --sidebar-bg: var(--card-dark);
      --muted: #8fa6bd;
    }

    html,body{height:100%;}
    body{
      font-family: "Public Sans", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      margin:0;
      background:var(--bs-body-bg);
      color:var(--bs-body-color);
      transition:background .25s ease, color .25s ease;
      display:flex;
      flex-direction:column;
    }

    /* Header (fixed) */
    header.topbar{
      height:64px;
      background:var(--nav-bg);
      border-bottom:1px solid var(--bs-border);
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:0 1.25rem;
      position:fixed;
      top:0; left:0; right:0;
      z-index:1030;
    }

    /* Sidebar (fixed) */
    .dashboard-wrap{display:flex; flex:1; margin-top:64px; min-height: calc(100vh - 64px);}
    aside.sidebar{
      width:260px;
      background:var(--sidebar-bg);
      border-right:1px solid var(--bs-border);
      padding:1rem;
      position:sticky; top:64px;
      height:calc(100vh - 64px);
      overflow:auto;
    }
    aside .brand{display:flex; gap:.6rem; align-items:center;}
    aside .brand .icon{
      width:44px; height:44px; border-radius:.6rem;
      display:flex; align-items:center; justify-content:center;
      color:var(--primary); background:rgba(17,115,212,0.08);
      font-size:1.4rem;
    }
    aside .nav-link{
      color:inherit;
      border-radius:.5rem;
      padding:.5rem .65rem;
      display:flex; gap:.6rem; align-items:center;
    }
    aside .nav-link.active{
      background: rgba(17,115,212,0.08);
      color:var(--primary);
      font-weight:600;
    }

    /* Main (scrollable) */
    main.content{
      flex:1;
      padding:1.5rem;
      overflow:auto;
      background:var(--bs-body-bg);
    }

    /* Cards */
    .stat-card{
      border:1px solid var(--bs-border);
      background:var(--card-bg);
      border-radius:.75rem;
      padding:1.1rem;
    }

    /* Case list & detail */
    .case-list .case-card{ border:1px solid var(--bs-border); background:var(--card-bg); border-radius:.75rem; padding:1rem; cursor:pointer;}
    .case-card.selected{ border-width:2px; border-color:var(--primary); background: rgba(17,115,212,0.04); }

    .detail-panel{
      border:1px solid var(--bs-border);
      background:var(--card-bg);
      border-radius:.75rem;
      padding:1rem;
      min-height:360px;
      display:flex; flex-direction:column;
    }

    /* media area */
    .video-thumb{
      position:relative;
      border-radius:.6rem;
      overflow:hidden;
      background:#000;
    }
    .video-thumb img{ width:100%; height:100%; object-fit:cover; opacity:.9; display:block; }
    .play-btn{
      position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
    }
    .play-btn .btn{ opacity:.95; backdrop-filter: blur(3px); }

    /* footer */
    footer.dashboard-footer{
      border-top:1px solid var(--bs-border);
      background:var(--card-bg);
      padding:.9rem 1rem;
      text-align:center;
    }

    /* utility small adjustments to match spacing */
    .gap-3x{gap:1rem;}
    .muted{color:var(--muted);}

    /* responsive: collapse sidebar on small screens */
    @media (max-width: 991.98px){
      aside.sidebar{ position:fixed; left:-280px; top:64px; height:calc(100vh - 64px); transition:left .25s ease; z-index:1040; }
      aside.sidebar.show{ left:0; }
      .sidebar-backdrop{ display:none; }
    }

    /* small icon circle for avatars */
    .avatar-sm{ width:44px; height:44px; border-radius:50%; overflow:hidden; display:inline-block; background-size:cover; background-position:center; }

    /* badges */
    .badge-status-high{ background:var(--status-high); color:#fff; }
    .badge-status-medium{ background:var(--status-medium); color:#000; }
    .badge-status-success{ background:var(--status-completed); color:#fff; }
    .badge-info{ background:var(--status-info); color:#fff; }

  </style>
</head>
<body>
  <!-- TOPBAR -->
  <header class="topbar">
    <div class="d-flex align-items-center gap-3">
      <div class="d-flex align-items-center gap-2">
        <div class="fs-4 text-primary"><i class="bi bi-shield-lock-fill"></i></div>
        <div>
          <h5 class="mb-0 fw-bold">Forensic Analysis Hub</h5>
          <small class="muted">Forensic Analyst Dashboard</small>
        </div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <div class="input-group d-none d-md-flex" style="width:350px;">
        <input id="searchInput" type="text" class="form-control form-control-sm bg-body-secondary border-0" placeholder="Search by Case ID, FIR...">
        <span class="input-group-text bg-body-secondary border-0"><i class="bi bi-search"></i></span>
      </div>

      <button id="sidebarToggle" class="btn btn-sm btn-light d-lg-none"><i class="bi bi-list"></i></button>

      <button class="btn btn-sm btn-outline-secondary" title="Notifications"><i class="bi bi-bell"></i></button>

      <!-- Theme toggle -->
      <button id="themeToggle" class="btn btn-sm btn-outline-secondary" title="Toggle theme"><i id="themeIcon" class="bi bi-moon"></i></button>

      <!-- user -->
      <div class="d-flex align-items-center gap-2 ms-2">
        <div class="avatar-sm" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuANtbpVG3nvyMmfB2b3hHSNk6USnOiPajbtkFFtVpz_7zjKn2XyotNHbu4lajaRJgm2oErZtAySt-1TxUSCeWYuJxqVFHsn6ZE41HoniZuLY0tHHorWF5xYIUq_kCmKi7KHlICqMDbirZjblwF8Bwg_l-i3cUcT_kwTCflKF2rds3UtXEd7xgewnoSzcEHit_-lY7XAVwmNzP6DhoC-zbiPR5Z0UjumDl505_OKfSqTwIUGtijwoxfztEoaKIUR31J6Kt2b1T6AvLF_');"></div>
        <div class="d-none d-md-block text-start">
          <div class="fw-semibold">Dr. Anya Sharma</div>
          <div class="small muted">Forensic Analyst</div>
        </div>
      </div>
    </div>
  </header>

  <!-- DASHBOARD WRAP: sidebar + main -->
  <div class="dashboard-wrap">

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div>
        <div class="brand mb-3">
          <div class="icon"><i class="bi bi-person-badge"></i></div>
          <div>
            <div class="fw-bold">FAH</div>
            <div class="small muted">Forensic Analysis Hub</div>
          </div>
        </div>

        <nav class="nav flex-column gap-2 mb-3">
          <a href="#" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
          <a href="#" class="nav-link"><i class="bi bi-folder"></i> All Cases</a>
          <a href="#" class="nav-link"><i class="bi bi-box-seam"></i> Evidence Locker</a>
          <a href="#" class="nav-link"><i class="bi bi-file-text"></i> Reports</a>
          <a href="#" class="nav-link"><i class="bi bi-gear"></i> Settings</a>
        </nav>
      </div>

      <div>
        <div class="d-grid gap-2 mb-3">
          <button class="btn btn-primary">New Case Intake</button>
        </div>

        <div class="border-top pt-3">
          <a href="#" class="d-flex align-items-center gap-2 nav-link"><i class="bi bi-question-circle"></i> Help Center</a>
          <a href="#" class="d-flex align-items-center gap-2 nav-link"><i class="bi bi-box-arrow-right"></i> Log Out</a>
        </div>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="content">
      <!-- page heading -->
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <h2 class="fw-bold mb-0">Forensic Analyst Dashboard</h2>
          <div class="muted">Welcome back, Dr. Anya Sharma</div>
        </div>
      </div>

      <!-- stats -->
      <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="muted small">New Cases Assigned</div>
            <div class="d-flex align-items-end gap-2">
              <div class="fs-3 fw-bold">12</div>
              <div class="text-success small">+2%</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="muted small">High-Priority Cases</div>
            <div class="d-flex align-items-end gap-2">
              <div class="fs-3 fw-bold">3</div>
              <div class="text-success small">+5%</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="muted small">Cases Pending Review</div>
            <div class="d-flex align-items-end gap-2">
              <div class="fs-3 fw-bold">8</div>
              <div class="text-danger small">-3%</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="muted small">Completed This Week</div>
            <div class="d-flex align-items-end gap-2">
              <div class="fs-3 fw-bold">5</div>
              <div class="text-success small">+10%</div>
            </div>
          </div>
        </div>
      </div>

      <!-- two-column layout: case list + detail -->
      <div class="row g-3">
        <!-- case list -->
        <div class="col-12 col-lg-5">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Assigned Cases</h5>
            <div class="d-flex gap-2">
              <button id="filterBtn" class="btn btn-sm btn-light"><i class="bi bi-funnel"></i> Filter</button>
              <button id="sortBtn" class="btn btn-sm btn-light"><i class="bi bi-funnel-fill"></i> Sort</button>
            </div>
          </div>

          <div class="case-list d-flex flex-column gap-3">
            <!-- selected -->
            <div class="case-card selected" data-case="FIR-2023-08-1123">
              <div class="d-flex justify-content-between">
                <div class="fw-bold">FIR-2023-08-1123</div>
                <div><span class="badge badge-status-high small px-2 py-1">High</span></div>
              </div>
              <div class="muted small">Robbery at Downtown Bank</div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bi bi-camera-video"></i>
                  <i class="bi bi-mic"></i>
                  <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="small muted">
                  <span class="badge bg-info text-white small">New Evidence Added</span>
                  <span class="ms-2">2 hours ago</span>
                </div>
              </div>
            </div>

            <div class="case-card" data-case="FIR-2023-08-1120">
              <div class="d-flex justify-content-between">
                <div class="fw-semibold">FIR-2023-08-1120</div>
                <div><span class="badge badge-status-medium small px-2 py-1">Medium</span></div>
              </div>
              <div class="muted small">Cyberbullying Complaint</div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bi bi-chat-text"></i>
                  <i class="bi bi-image"></i>
                </div>
                <div class="small muted">
                  <span class="badge bg-info text-white small">In Progress</span>
                  <span class="ms-2">1 day ago</span>
                </div>
              </div>
            </div>

            <div class="case-card" data-case="FIR-2023-08-1115">
              <div class="d-flex justify-content-between">
                <div class="fw-semibold">FIR-2023-08-1115</div>
                <div><span class="badge bg-secondary small px-2 py-1">Low</span></div>
              </div>
              <div class="muted small">Vandalism Report</div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="small muted">
                  <span class="badge bg-secondary small">Pending Police Input</span>
                  <span class="ms-2">3 days ago</span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- detail panel -->
        <div class="col-12 col-lg-7">
          <div class="detail-panel">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
              <div>
                <h5 class="mb-0">Viewing Case: <span class="text-primary">FIR-2023-08-1123</span></h5>
              </div>
              <div>
                <button class="btn btn-primary btn-sm" id="startAnalysisBtn"><i class="bi bi-gear"></i> Start Analysis</button>
              </div>
            </div>

            <!-- tabs -->
            <div class="mb-3 border-bottom">
              <ul class="nav nav-tabs" id="detailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#aiAnalysis" type="button" role="tab">AI Analysis Suite</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#evidenceLocker" type="button" role="tab">Evidence Locker</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#caseSummary" type="button" role="tab">Case Summary</button>
                </li>
              </ul>
            </div>

            <div class="tab-content flex-grow-1" style="min-height:220px;">
              <div class="tab-pane fade show active" id="aiAnalysis" role="tabpanel">
                <h6 class="fw-semibold">Video & Image Analysis</h6>

                <div class="video-thumb mb-2" style="height:260px;">
                  <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxI3mxwGkqfrKBDWpN0b5TisIqCSknPfTUXF36K79Z56jlAIv8EhlWxjKl38yn6ABTEd2tknzUSumJu0teVYsIXLBoXFTNHnATLmIqrtMZsM3n3VThscXso8UjiyAbN5PdPOzWtgcxw5k9532e-Vm4unkJmvdytYvfFBztcoY3GqXQVsjj8C_4LDunaa8QsitHXJRngSbrY2fsnJWsnKQrgXaodotu0q7J7qvVjC5kz9QLnGPhAfBnIVh6iMXgU0bGGIqOxjkGfCEx" alt="cctv" class="w-100 h-100 object-fit-cover">
                  <div class="play-btn">
                    <button id="playVideo" class="btn btn-light btn-lg rounded-circle shadow"><i class="bi bi-play-fill fs-3"></i></button>
                  </div>
                </div>

                <p class="muted small">CCTV_Main_Lobby_1.mp4 - Face detection markers & object recognition available.</p>

                <div class="mt-3">
                  <h6 class="fw-semibold">Audio Analysis</h6>
                  <div class="p-3 rounded d-flex gap-3" style="background:var(--card-bg); border:1px solid var(--bs-border);">
                    <div style="width:42%; background:linear-gradient(90deg,#e9ecef,#dee2e6);" class="rounded"></div>
                    <div style="flex:1;">
                      <div class="fw-medium small">AI-Generated Transcript</div>
                      <div class="muted small">"Speaker 1: Give me all the money... Speaker 2: (muffled)..."</div>
                    </div>
                  </div>
                </div>

              </div>

              <div class="tab-pane fade" id="evidenceLocker" role="tabpanel">
                <h6 class="fw-semibold">Evidence Locker</h6>
                <p class="muted small">List of uploaded media, documents and annotations.</p>
              </div>

              <div class="tab-pane fade" id="caseSummary" role="tabpanel">
                <h6 class="fw-semibold">Case Summary</h6>
                <p class="muted small">Auto-generated summary and extracted findings ready for review.</p>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- footer -->
      <footer class="dashboard-footer mt-4">
        <div class="container">
          <div class="d-flex justify-content-between align-items-center small">
            <div>&copy; <span id="year"></span> Centralized Digital Evidence Management System</div>
            <div>
              <a href="#" class="text-decoration-none me-3 footer-link">Privacy Policy</a>
              <a href="#" class="text-decoration-none me-3 footer-link">Terms</a>
              <a href="#" class="text-decoration-none footer-link">Support</a>
            </div>
          </div>
        </div>
      </footer>
    </main>

  </div> <!-- /dashboard-wrap -->

  <!-- jQuery (for simple interactions/AJAX) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Bootstrap bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    (function(){
      const html = document.documentElement;
      const themeToggle = document.getElementById('themeToggle');
      const themeIcon = document.getElementById('themeIcon');

      function setTheme(mode){
        html.setAttribute('data-bs-theme', mode);
        localStorage.setItem('theme', mode);
        if(mode === 'dark'){
          themeIcon.className = 'bi bi-sun-fill';
        } else {
          themeIcon.className = 'bi bi-moon';
        }
      }

      // initialize theme
      const saved = localStorage.getItem('theme') || 'light';
      setTheme(saved);

      themeToggle.addEventListener('click', ()=>{
        const cur = html.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
        setTheme(cur === 'dark' ? 'light' : 'dark');
      });

      // sidebar toggle for mobile
      $('#sidebarToggle').on('click', function(){
        $('.sidebar').toggleClass('show');
        // simple backdrop (optional)
        if($('.sidebar').hasClass('show')){
          if(!$('.sidebar-backdrop').length){
            $('<div class="sidebar-backdrop"></div>').appendTo('body').css({
              position:'fixed', inset:0, zIndex:1035, background:'rgba(0,0,0,0.3)'
            }).on('click', function(){
              $('.sidebar').removeClass('show'); $(this).remove();
            });
          }
        }else{
          $('.sidebar-backdrop').remove();
        }
      });

      // case selection
      $('.case-list').on('click', '.case-card', function(){
        $('.case-card').removeClass('selected');
        $(this).addClass('selected');
        const caseId = $(this).data('case');
        // update detail panel header & content (simple)
        $('.detail-panel h5 span.text-primary').text(caseId);
        // optionally load details via AJAX:
        // $.get('/api/cases/'+caseId, function(data){ ... });
      });

      // play video (demo)
      $('#playVideo').on('click', function(){
        alert('Play video (demo) — replace with modal or player.');
      });

      // filter & sort demo (replace with actual behavior)
      $('#filterBtn').on('click', function(){
        // Example AJAX placeholder:
        // $.get('/api/cases?filter=...', function(html){ $('.case-list').html(html); });
        alert('Filter clicked — implement filter modal / AJAX as needed');
      });
      $('#sortBtn').on('click', function(){
        alert('Sort clicked — implement sorting logic here');
      });

      // search demo (simple highlight by case id)
      $('#searchInput').on('input', function(){
        const q = $(this).val().trim().toLowerCase();
        if(!q) { $('.case-card').show(); return; }
        $('.case-card').each(function(){
          const text = $(this).text().toLowerCase();
          $(this).toggle(text.indexOf(q) !== -1);
        });
      });

      // footer year
      document.getElementById('year').textContent = new Date().getFullYear();

      // ensure theme of footer links (no separate listener needed since CSS uses variables)
      window.addEventListener('storage', ()=> setTheme(localStorage.getItem('theme') || 'light'));
    })();
  </script>
</body>
</html> --}}
@extends('forensic_analyst.layouts.app')

@section('title','Dashboard')

@section('content')
<div class="row g-3">
   <!-- MAIN -->
    
      <!-- page heading -->
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <h2 class="fw-bold mb-0">Forensic Analyst Dashboard</h2>
          <div class="muted">Welcome back, Dr. Anya Sharma</div>
        </div>
      </div>

      <!-- stats -->
      <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="muted small">New Cases Assigned</div>
            <div class="d-flex align-items-end gap-2">
              <div class="fs-3 fw-bold">12</div>
              <div class="text-success small">+2%</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="muted small">High-Priority Cases</div>
            <div class="d-flex align-items-end gap-2">
              <div class="fs-3 fw-bold">3</div>
              <div class="text-success small">+5%</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="muted small">Cases Pending Review</div>
            <div class="d-flex align-items-end gap-2">
              <div class="fs-3 fw-bold">8</div>
              <div class="text-danger small">-3%</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="muted small">Completed This Week</div>
            <div class="d-flex align-items-end gap-2">
              <div class="fs-3 fw-bold">5</div>
              <div class="text-success small">+10%</div>
            </div>
          </div>
        </div>
      </div>

      <!-- two-column layout: case list + detail -->
      <div class="row g-3">
        <!-- case list -->
        <div class="col-12 col-lg-5">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Assigned Cases</h5>
            <div class="d-flex gap-2">
              <button id="filterBtn" class="btn btn-sm btn-light"><i class="bi bi-funnel"></i> Filter</button>
              <button id="sortBtn" class="btn btn-sm btn-light"><i class="bi bi-funnel-fill"></i> Sort</button>
            </div>
          </div>

          <div class="case-list d-flex flex-column gap-3">
            <!-- selected -->
            <div class="case-card selected" data-case="FIR-2023-08-1123">
              <div class="d-flex justify-content-between">
                <div class="fw-bold">FIR-2023-08-1123</div>
                <div><span class="badge badge-status-high small px-2 py-1">High</span></div>
              </div>
              <div class="muted small">Robbery at Downtown Bank</div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bi bi-camera-video"></i>
                  <i class="bi bi-mic"></i>
                  <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="small muted">
                  <span class="badge bg-info text-white small">New Evidence Added</span>
                  <span class="ms-2">2 hours ago</span>
                </div>
              </div>
            </div>

            <div class="case-card" data-case="FIR-2023-08-1120">
              <div class="d-flex justify-content-between">
                <div class="fw-semibold">FIR-2023-08-1120</div>
                <div><span class="badge badge-status-medium small px-2 py-1">Medium</span></div>
              </div>
              <div class="muted small">Cyberbullying Complaint</div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bi bi-chat-text"></i>
                  <i class="bi bi-image"></i>
                </div>
                <div class="small muted">
                  <span class="badge bg-info text-white small">In Progress</span>
                  <span class="ms-2">1 day ago</span>
                </div>
              </div>
            </div>

            <div class="case-card" data-case="FIR-2023-08-1115">
              <div class="d-flex justify-content-between">
                <div class="fw-semibold">FIR-2023-08-1115</div>
                <div><span class="badge bg-secondary small px-2 py-1">Low</span></div>
              </div>
              <div class="muted small">Vandalism Report</div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="small muted">
                  <span class="badge bg-secondary small">Pending Police Input</span>
                  <span class="ms-2">3 days ago</span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- detail panel -->
        <div class="col-12 col-lg-7">
          <div class="detail-panel">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
              <div>
                <h5 class="mb-0">Viewing Case: <span class="text-primary">FIR-2023-08-1123</span></h5>
              </div>
              <div>
                <button class="btn btn-primary btn-sm" id="startAnalysisBtn"><i class="bi bi-gear"></i> Start Analysis</button>
              </div>
            </div>

            <!-- tabs -->
            <div class="mb-3 border-bottom">
              <ul class="nav nav-tabs" id="detailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#aiAnalysis" type="button" role="tab">AI Analysis Suite</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#evidenceLocker" type="button" role="tab">Evidence Locker</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#caseSummary" type="button" role="tab">Case Summary</button>
                </li>
              </ul>
            </div>

            <div class="tab-content flex-grow-1" style="min-height:220px;">
              <div class="tab-pane fade show active" id="aiAnalysis" role="tabpanel">
                <h6 class="fw-semibold">Video & Image Analysis</h6>

                <div class="video-thumb mb-2" style="height:260px;">
                  <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxI3mxwGkqfrKBDWpN0b5TisIqCSknPfTUXF36K79Z56jlAIv8EhlWxjKl38yn6ABTEd2tknzUSumJu0teVYsIXLBoXFTNHnATLmIqrtMZsM3n3VThscXso8UjiyAbN5PdPOzWtgcxw5k9532e-Vm4unkJmvdytYvfFBztcoY3GqXQVsjj8C_4LDunaa8QsitHXJRngSbrY2fsnJWsnKQrgXaodotu0q7J7qvVjC5kz9QLnGPhAfBnIVh6iMXgU0bGGIqOxjkGfCEx" alt="cctv" class="w-100 h-100 object-fit-cover">
                  <div class="play-btn">
                    <button id="playVideo" class="btn btn-light btn-lg rounded-circle shadow"><i class="bi bi-play-fill fs-3"></i></button>
                  </div>
                </div>

                <p class="muted small">CCTV_Main_Lobby_1.mp4 - Face detection markers & object recognition available.</p>

                <div class="mt-3">
                  <h6 class="fw-semibold">Audio Analysis</h6>
                  <div class="p-3 rounded d-flex gap-3" style="background:var(--card-bg); border:1px solid var(--bs-border);">
                    <div style="width:42%; background:linear-gradient(90deg,#e9ecef,#dee2e6);" class="rounded"></div>
                    <div style="flex:1;">
                      <div class="fw-medium small">AI-Generated Transcript</div>
                      <div class="muted small">"Speaker 1: Give me all the money... Speaker 2: (muffled)..."</div>
                    </div>
                  </div>
                </div>

              </div>

              <div class="tab-pane fade" id="evidenceLocker" role="tabpanel">
                <h6 class="fw-semibold">Evidence Locker</h6>
                <p class="muted small">List of uploaded media, documents and annotations.</p>
              </div>

              <div class="tab-pane fade" id="caseSummary" role="tabpanel">
                <h6 class="fw-semibold">Case Summary</h6>
                <p class="muted small">Auto-generated summary and extracted findings ready for review.</p>
              </div>
            </div>

          </div>
        </div>
      </div>

     
 
</div>
@endsection
