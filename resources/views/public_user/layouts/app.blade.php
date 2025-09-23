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

 <link href="{{ asset('css/public_user/app.css') }}" rel="stylesheet">
  @stack('styles')

</head>
<body>
@php
            $user =Auth::user();
          @endphp
          
            
          
<!-- Navbar -->
@auth
  

@if( Route::is('public.welcome')===false)
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

    <div class="d-flex flex-column flex-md-row align-items-center gap-3 gap-md-4">

     
        
       <!-- Notifications & User Profile -->
<div class="d-flex align-items-center gap-2 ms-auto">
    
    <!-- Notifications -->
    <button class="btn p-2 rounded-circle position-relative">
        <span class="material-symbols-outlined text-secondary">notifications</span>
        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
    </button>
 @php
            $user =Auth::user();
          @endphp
     
  <!-- User Profile -->
  <div class="nav-item topbar-user dropdown hidden-caret ">
    @include('partials.profile-dropdown')
  </div>

</div>


  </div>
  </div>


     <!-- Profile Edit Modal -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label>Name</label>
              <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password (leave blank if not changing)</label>
              <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
              <label>Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="mb-3">
              <label>CNIC</label>
              <input type="text" name="cnic" value="{{ $user->cnic }}" class="form-control">
            </div>
            <div class="mb-3">
              <label>Contact Number</label>
              <input type="text" name="contact_number" value="{{ $user->contact_number }}" class="form-control">
            </div>
            <div class="mb-3">
              <label>Profile Image</label>
              <input type="file" name="profile_image" class="form-control">
              @if($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" class="img-thumbnail mt-2" width="100">
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Update Profile</button>
          </div>
        </form>
      </div>
    </div>
  </div>



</header>
@endif
@endauth
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


<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Bundle with Popper -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Core JS Files -->
<script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
{{-- <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

<!-- jQuery Scrollbar -->
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

<!-- Chart JS -->
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>


<!-- jQuery Sparkline -->
<script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Chart Circle -->
<script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- jQuery Vector Maps -->
<script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>

<!-- Sweet Alert -->
<script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Kaiadmin JS -->
<script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

