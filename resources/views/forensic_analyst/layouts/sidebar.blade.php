{{-- <aside class="sidebar">
    <div>
        <div class="brand mb-3">
            <div class="icon"><i class="bi bi-person-badge"></i></div>
            <div>
                <div class="fw-bold">FAH</div>
                <div class="small muted">Forensic Analysis Hub</div>
            </div>
        </div>

        <nav class="nav flex-column gap-2 mb-3">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="{{ route('cases.index') }}" class="nav-link {{ request()->routeIs('cases.*') ? 'active' : '' }}"><i class="bi bi-folder"></i> All Cases</a>
            <a href="{{ route('evidence.index') }}" class="nav-link {{ request()->routeIs('evidence.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Evidence Locker</a>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"><i class="bi bi-file-text"></i> Reports</a>
            <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}"><i class="bi bi-gear"></i> Settings</a>
        </nav>
    </div>

    <div>
        <div class="d-grid gap-2 mb-3">
            <a href="{{ route('cases.create') }}" class="btn btn-primary">New Case Intake</a>
        </div>

        <div class="border-top pt-3">
            <a href="{{ route('help') }}" class="d-flex align-items-center gap-2 nav-link"><i class="bi bi-question-circle"></i> Help Center</a>
            <a href="{{ route('logout') }}" class="d-flex align-items-center gap-2 nav-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               <i class="bi bi-box-arrow-right"></i> Log Out
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>
</aside> --}}
  <!-- SIDEBAR -->
    <aside class="sidebar d-flex flex-column justify-content-between">
      <div>
     

        <nav class="nav flex-column gap-2 mb-3 mt-2">
          {{-- <a href="#" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
          <a href="#" class="nav-link"><i class="bi bi-folder"></i> All Cases</a> --}}
                      <a href="{{ route('forensic.dashboard') }}" class="nav-link {{ request()->routeIs('forensic.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="{{ route('forensic.assigned-cases') }}" class="nav-link {{ request()->routeIs('forensic.assigned-cases') ? 'active' : '' }}"><i class="bi bi-folder"></i> All Cases</a>
          <a href="#" class="nav-link"><i class="bi bi-box-seam"></i> Evidence Locker</a>
          <a href="#" class="nav-link"><i class="bi bi-file-text"></i> Reports</a>
          
        </nav>
      </div>

      {{-- <div>
        <div class="d-grid gap-2 mb-3">
          <button class="btn btn-primary">New Case Intake</button>
        </div> --}}

        <div class="border-top pt-3">
          
          <a href="#" class="d-flex align-items-center gap-2 nav-link"><i class="bi bi-gear"></i> Settings</a>
          <a href="#" class="d-flex align-items-center gap-2 nav-link"><i class="bi bi-box-arrow-right"></i> Log Out</a>
        </div>
      </div>
    </aside>