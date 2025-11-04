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


  <!-- Font Awesome 6 CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

 <link href="{{ asset('css/public_user/app.css') }}" rel="stylesheet">
  @stack('styles')

</head>
<body>
@php
            $user =Auth::user();
          @endphp
          
            
          
<!-- Navbar -->
@guest
@if (Route::is('public.welcome')===false)
<header class="sticky-top bg-white shadow-sm">
  <div class="container py-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-5 ">

      <nav class="navbar navbar-expand-lg navbar-light bg-white px-3 custom-gap">
 <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar"
      aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Logo -->
    <a href="{{ route('public.welcome') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark d-none d-lg-flex">
      <span class="material-symbols-outlined fs-2 text-primary">shield</span>
      <h5 class="m-0 fw-bold">Citizen Connect</h5>
    </a>
    <!-- Links -->
    <div class="collapse navbar-collapse" id="publicNavbar">
      <ul class="navbar-nav ms-auto gap-lg-3">

        <li class="nav-item" >
              <a href="{{ route('public.welcome') }}" class="nav-link {{ Route::is('public.welcome') ? 'active' : '' }}">Dashboard</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('public.complaints.form') }}" class="nav-link {{ Route::is('public.complaints.form') ? 'active' : '' }}" >Complaints</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('public.complaints.track') }}" class="nav-link {{ Route::is('public.complaints.track') ? 'active' : '' }}" >Track Complaints</a>
            </li>
              <li class="nav-item">
              <a href="{{ route('public.alerts') }}" class="nav-link {{ Route::is('public.alerts') ? 'active' : '' }}" >Alerts</a>
            </li>
          </ul>
        </div>
      </nav>
    </div>
    <div class="d-flex flex-column flex-md-row align-items-center gap-3 gap-md-4">
      <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
      <a class="btn btn-secondary ms-2" href="{{ route('register') }}">Register</a>
      
    </div>
  </div>
</header>
@endif
@endguest

@if (Auth::user())
@auth
@if( Route::is('public.welcome')===false)
<header class="sticky-top bg-white shadow-sm">
  
  <div class="container-fluid py-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-5 ">

      <nav class="navbar navbar-expand-lg navbar-light bg-white px-3 custom-gap">
 <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar"
      aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Logo -->
    <a href="{{ route('public.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark d-none d-lg-flex">
      <span class="material-symbols-outlined fs-2 text-primary">shield</span>
      <h5 class="m-0 fw-bold">Citizen Connect</h5>
    </a>

    

    <!-- Links -->
    <div class="collapse navbar-collapse" id="publicNavbar">
      <ul class="navbar-nav ms-auto gap-lg-3">

            <li class="nav-item" >
              <a href="{{ route('public.dashboard') }}" class="nav-link {{ Route::is('public.dashboard') ? 'active' : '' }}">Dashboard</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('public.complaints.form') }}" class="nav-link {{ Route::is('public.complaints.form') ? 'active' : '' }}" >Complaints</a>
            </li>
          <li class="nav-item">
          <a href="{{ route('public.complaints.track') }}" 
   class="nav-link {{ Route::is('public.complaints.track', 'complaints.track.submit') ? 'active' : '' }}">
   Track Complaints
</a>

          </li>
            <li class="nav-item">
              <a href="{{ route('public.alerts') }}" class="nav-link {{ Route::is('public.alerts') ? 'active' : '' }}">Alerts</a>
            </li>


      </ul>
    </div>
  
</nav>
   

    </div>
{{-- user profile and notification --}}
 <div class="d-flex flex-md-row align-items-center gap-3 gap-md-4">

  <!-- Notifications -->
  <div class="dropdown">
     <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown"
   aria-haspopup="true" aria-expanded="false">
   <i class="fa-solid fa-bell"></i>
   <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
      4
   </span>
</a>


    <!-- Notification Dropdown -->
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
      <li class="px-3 py-2 border-bottom">
        <strong>Notifications</strong>
      </li>
      <li>
        <a class="dropdown-item d-flex align-items-start gap-2 py-2" href="#">
          <i class="fas fa-info-circle text-primary mt-1 fs-2 mt-2"></i>
          <div>
            <small class="fw-bold">System Update</small><br>
            <small class="text-muted">New version deployed successfully.</small>
          </div>
        </a>
      </li>
      <li>
        <a class="dropdown-item d-flex align-items-start gap-2 py-2" href="#">
          <i class="fas fa-user text-success fs-2 mt-2"></i>
          <div>
            <small class="fw-bold">New User</small><br>
            <small class="text-muted">John Doe registered.</small>
          </div>
        </a>
      </li>
      <li>
        <a class="dropdown-item d-flex align-items-start gap-2 py-2" href="#">
          <i class="fas fa-exclamation-triangle text-warning fs-2 mt-2"></i>
          <div>
            <small class="fw-bold">Alert</small><br>
            <small class="text-muted">Unusual activity detected.</small>
          </div>
        </a>
      </li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <a class="dropdown-item text-center fw-semibold" href="#">View All</a>
      </li>
    </ul>
  </div>

  <!-- User Profile -->
  <div class="d-flex align-items-center ms-auto">
    @php
      $user = Auth::user();
    @endphp

    @if($user)
    <!-- Profile Dropdown Trigger -->
    <a class="d-flex align-items-center gap-2 text-decoration-none" 
       data-bs-toggle="dropdown" href="#" aria-expanded="false">
      
             @if(!empty($user->profile_image))
            {{-- Profile Image --}}
  <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/profile.jpg') }}" 
             class="rounded-circle border shadow-sm" 
             style="width:42px; height:42px; object-fit:cover;" />
        @else
            {{-- Fallback: Initials --}}
            @php
                $nameParts = explode(' ', trim($user->name ?? ''));
                $initials = strtoupper(substr($nameParts[0] ?? 'N', 0, 1));
                if (count($nameParts) > 1) {
                    $initials .= strtoupper(substr($nameParts[1], 0, 1));
                }
            @endphp
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" 
                 style="width:40px; height:40px; font-weight:600; font-size:16px;">
                {{ $initials }}
            </div>
        @endif
        <span class="fw-semibold text-primary d-none d-md-inline">{{ $user->name }}</span>
    </a>


    <!-- Dropdown -->
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2">

  <li class=" py-2">
    <div class="d-flex align-items-center text-start" style="max-width: 220px; margin:auto;">
        <!-- Profile Image -->
        <div class="d-flex justify-content-center align-items-center" style="width:80px;">

                 @if(!empty($user->profile_image))
            {{-- Profile Image --}}
            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/profile.jpg') }}" 
                 class="rounded-circle border shadow-sm" 
                 style="width:70px; height:70px; object-fit:cover;" />
        @else
            {{-- Fallback: Initials --}}
            @php
                $nameParts = explode(' ', trim($user->name ?? ''));
                $initials = strtoupper(substr($nameParts[0] ?? 'N', 0, 1));
                if (count($nameParts) > 1) {
                    $initials .= strtoupper(substr($nameParts[1], 0, 1));
                }
            @endphp
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" 
                 style="width:70px; height:70px; font-weight:600; font-size:24px;">
                {{ $initials }}
            </div>
        @endif
        </div>

        <!-- Name, Email & Button -->
        <div class="flex-grow-1">
            <h6 class="mb-0 fw-semibold" style="font-size: 14px; max-width:120px; word-break: break-word;">
                {{ $user->name }}
            </h6>
            <small class="text-muted d-block" style="font-size: 12px; max-width:125px; word-break: break-word;">
                {{ $user->email }}
            </small>
            <div class="mt-2">
                <button type="button" style="font-size: 12px" class="btn btn-sm btn-primary px-2 " data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </button>
            </div>
        </div>
    </div>
</li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewProfileModal{{ $user->id }}"><i class="fas fa-user me-1"></i> My Profile</a></li>
        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Account Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
    @else
    <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
    <a class="btn btn-outline-secondary ms-2" href="{{ route('register') }}">Register</a>
    @endif
  </div>
</div>

</div>

</header>
@endif
@include('partials.edit-profile-modal')
@include('partials.view-profile-model')
@endauth
@endif
<!-- Page Content -->
<main class="container ">
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
          <p class="text-secondary small mb-0">&copy; <script>document.write(new Date().getFullYear());</script> CrimeWatch. All rights reserved.</p>
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
<script src="layouts."></script>
</body>
</html>

