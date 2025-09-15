<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Citizen Connect')</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans:wght@400;500;700;900&family=Public+Sans:wght@400;500;700;900" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

 <link href="{{ asset('css/public user/app.css') }}" rel="stylesheet">
  @stack('styles')

</head>
<body>
@php
            $user =Auth::user();
          @endphp
<!-- Navbar -->
<header class="sticky-top bg-white shadow-sm">
  <div class="container py-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-4">
      <a href="{{ route('public.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
        <span class="material-symbols-outlined fs-2 text-primary">shield</span>
        <h4 class="m-0">Citizen Connect</h4>
      </a>
      <nav class="d-none d-md-flex gap-4">
        {{-- Complaints Form page --}}
@if(Route::is('public.complaints.form'))
    <a href="{{ route('public.dashboard') }}" class="nav-link">Dashboard</a>
    <a href="{{ route('public.complaints.track') }}" class="nav-link">Track My Complaints</a>

{{-- Dashboard page --}}
@elseif(Route::is('public.dashboard'))
    <a href="{{ route('public.dashboard') }}" class="nav-link">Dashboard</a>
    <a href="{{ route('public.complaints.form') }}" class="nav-link">Complaints</a>
    <a href="{{ route('public.alerts') }}" class="nav-link">Alerts</a>
@endif

      </nav>
    </div>

    <div class="d-flex flex-column flex-md-row align-items-center gap-3 gap-md-4">

        <!-- Search Bar -->
        <div class="d-none d-md-flex position-relative">
            <span class="material-symbols-outlined position-absolute top-50 translate-middle-y ms-2 text-secondary">search</span>
            <input type="text" class="form-control ps-5" placeholder="Search"/>
        </div>
        
        <!-- Notifications -->
        <button class="btn p-2 rounded-circle position-relative">
            <span class="material-symbols-outlined text-secondary">notifications</span>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
        </button>
        
        <!-- User Profile -->
        <div class="nav-item dropdown hidden-caret">
    <a class="nav-link d-flex align-items-center gap-2 " 
       href="#" 
       id="userDropdown" 
       data-bs-toggle="dropdown" 
       aria-expanded="false">
        
        <!-- Profile Image -->
        <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/profile.jpg') }}" 
             class="avatar-img rounded-circle profile-img" 
             alt="Profile">

        <!-- User Name -->
        <span class="d-none d-lg-inline small fw-medium">{{ $user->name }}</span>
    </a>

    <!-- Dropdown Menu -->
    <ul class="dropdown-menu dropdown-menu-end " aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="{{ route('public.profile.update') }}">Profile</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
    </ul>
  </div>

  </div>
</header>

<!-- Page Content -->
<main class="container py-5">
  @yield('content')
</main>

  <!-- Footer -->
  <footer class="py-4">
    <div class="container d-flex flex-column flex-lg-row justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
       <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="me-2" viewBox="0 0 48 48">
          <path d="M44 11.2727C44 14.0109 39.8386 16.3957 33.69 17.6364C39.8386 18.877 44 21.2618 44 24C44 26.7382 39.8386 29.123 33.69 30.3636C39.8386 31.6043 44 33.9891 44 36.7273C44 40.7439 35.0457 44 24 44C12.9543 44 4 40.7439 4 36.7273C4 33.9891 8.16144 31.6043 14.31 30.3636C8.16144 29.123 4 26.7382 4 24C4 21.2618 8.16144 18.877 14.31 17.6364C8.16144 16.3957 4 14.0109 4 11.2727C4 7.25611 12.9543 4 24 4C35.0457 4 44 7.25611 44 11.2727Z"/>
        </svg>
        <span class="fw-bold">CrimeWatch</span>
      </div>
      <div class="mt-4 mt-lg-0 text-center text-secondary ">
          <p class="text-secondary small mb-0">&copy; 2024 CrimeWatch. All rights reserved.</p>
          <div class="mt-4 text-center text-secondary small">
              <a   class="text-decoration-none text-dark ms-4" href="#">Privacy Policy</a>
              <a class="text-decoration-none text-dark ms-4" href="#">Terms of Service</a>
              <a class="text-decoration-none text-dark ms-4" href="#">Contact Us</a>
            </div>
        </div>
  </footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


