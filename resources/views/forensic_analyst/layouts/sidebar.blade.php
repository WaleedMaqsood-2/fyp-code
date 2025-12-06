<!-- SIDEBAR -->
<aside class="sidebar d-flex flex-column justify-content-between">
    <!-- Mobile Close Button (Only visible on mobile) -->
    <div class="d-lg-none d-flex justify-content-end p-3 border-bottom">
        <button class="btn btn-sm btn-outline-secondary" id="closeSidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <div class="sidebar-content">
        <nav class="nav flex-column gap-2 mb-3 mt-2">
            <!-- Dashboard -->
            <a href="{{ route('forensic.dashboard') }}" class="nav-link {{ request()->routeIs('forensic.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> 
                <span class="sidebar-text">Dashboard</span>
            </a>

            <!-- Assigned Cases -->
            <a href="{{ route('forensic.assigned-cases') }}" class="nav-link {{ request()->routeIs('forensic.assigned-cases') ? 'active' : '' }}">
                <i class="bi bi-folder"></i>
                <span class="sidebar-text">Assigned Cases</span>
            </a>

            <!-- Face Matching -->
            <a href="{{ route('forensic.face.match') }}" class="nav-link {{ request()->routeIs('forensic.face.match') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i>
                <span class="sidebar-text">Face Matching</span>
            </a>

            <!-- Audio/Video Segmentation -->
            <a href="{{ route('forensic.audio-video') }}" class="nav-link {{ request()->routeIs('forensic.audio-video') ? 'active' : '' }}">
                <i class="bi bi-camera-reels"></i>
                <span class="sidebar-text">Media Segmentation</span>
            </a>

            <!-- AI Transcript Verification -->
            <a href="{{ route('forensic.transcript') }}" class="nav-link {{ request()->routeIs('forensic.transcript') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span class="sidebar-text">Transcript Verification</span>
            </a>

            <!-- AI Summary Approval -->
            <a href="{{ route('forensic.summary') }}" class="nav-link {{ request()->routeIs('forensic.summary') ? 'active' : '' }}">
                <i class="bi bi-card-checklist"></i>
                <span class="sidebar-text">Summary Approval</span>
            </a>

            <!-- Finalize Forensic Report -->
            <a href="{{ route('forensic.finalize') }}" class="nav-link {{ request()->routeIs('forensic.finalize') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-pdf"></i>
                <span class="sidebar-text">Finalize Report</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-footer border-top pt-3">
        <a href="#" class="d-flex align-items-center gap-2 nav-link">
            <i class="bi bi-gear"></i>
            <span class="sidebar-text">Settings</span>
        </a>
        <a href="#" class="d-flex align-items-center gap-2 nav-link">
            <i class="bi bi-box-arrow-right"></i>
            <span class="sidebar-text">Log Out</span>
        </a>
    </div>
</aside>

<!-- Mobile Toggle Button (in header) -->
<button id="mobileMenuToggle" class="btn btn-sm btn-light d-lg-none ms-2">
    <i class="bi bi-list"></i>
</button>

<!-- Mobile Overlay (when sidebar is open) -->
<div class="sidebar-overlay d-lg-none"></div>