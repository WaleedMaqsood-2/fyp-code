<div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
              <p class="text-white fs-4 navbar-brand" height="20">Admin Dashboard</p>
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" >
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item {{ Route::currentRouteName() == 'manage.users' ? 'active' : '' }}">
                <a href="{{ route('manage.users') }}">
                  <i class="fas fa-users-cog"></i>
                  <p>Manage Users</p>
                </a>
              </li>
              <li class="nav-item {{ Route::currentRouteName() == 'manage.media' ? 'active' : '' }}">
                <a href="{{ route('manage.media') }}">
               <i class="fas fa-images"></i>
                  <p>Manage Media</p>
                </a>
              </li>
              <li class="nav-item {{ Route::currentRouteName() == 'ai.usage' ? 'active' : '' }}">
                <a href="{{ route('ai.usage') }}">
                  <i class="fas fa-robot"></i>
                  <p>AI Usage</p>
                </a>
              </li>
              <li class="nav-item {{ Route::currentRouteName() == 'ai.feedback' ? 'active' : '' }}">
                <a href="{{ route('ai.feedback') }}">
                  <i class="fas fa-comments"></i>
                  <p>AI Feedback</p>
                </a>
              </li>
              <li class="nav-item {{ Route::currentRouteName() == 'analytics' ? 'active' : '' }}">
                <a href="{{ route('analytics') }}">
                  <i class="fas fa-chart-line"></i>
                  <p>Analytics</p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->
