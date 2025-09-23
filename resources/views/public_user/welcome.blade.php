@extends('public_user.layouts.app')

@section('title', 'Welcome to CrimeWatch')
@push('styles')
  <link href="{{ asset('css/public_user/welcome.css') }}" rel="stylesheet">
@endpush
@section('content')

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
    <div class="container my-2">
      <a class="navbar-brand d-flex align-items-center fw-bold text-dark" href="#">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="me-2" viewBox="0 0 48 48">
          <path d="M44 11.2727C44 14.0109 39.8386 16.3957 33.69 17.6364C39.8386 18.877 44 21.2618 44 24C44 26.7382 39.8386 29.123 33.69 30.3636C39.8386 31.6043 44 33.9891 44 36.7273C44 40.7439 35.0457 44 24 44C12.9543 44 4 40.7439 4 36.7273C4 33.9891 8.16144 31.6043 14.31 30.3636C8.16144 29.123 4 26.7382 4 24C4 21.2618 8.16144 18.877 14.31 17.6364C8.16144 16.3957 4 14.0109 4 11.2727C4 7.25611 12.9543 4 24 4C35.0457 4 44 7.25611 44 11.2727Z"/>
        </svg>
        CrimeWatch
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link fw-medium mx-lg-4" href="{{ route('public.welcome') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link fw-medium mx-lg-4" href="">About</a></li>
          <li class="nav-item"><a class="nav-link fw-medium mx-lg-4" href="{{ route('public.complaints.form') }}">Complaints</a></li>
          <li class="nav-item"><a class="nav-link fw-medium mx-lg-4" href="">Alerts</a></li>
        </ul>
        <div class="d-flex gap-2">
          <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
          <a href="{{ route('register') }}" class="btn btn-outline-dark">Sign Up</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section class="hero-section d-flex align-items-center">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center text-sm-start">
      <h1 class="display-4 fw-bold">Welcome to 
        <span class="text-primary">CrimeWatch</span>
      </h1>
      <p class="lead mt-3">Your centralized platform for managing complaints, FIRs, evidence, and public alerts. Stay informed and secure with our AI-powered analysis.</p>
      <div class="mt-4 d-flex gap-3 flex-wrap">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-lg-5">Login</a>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-lg-5">Sign Up</a>
      </div>
    </div>
  </section>

  <!-- Public Alerts -->
  <section class="py-5 bg-white">
    <div class="container text-center">
      <h2 class="fw-bold mb-3">Public Alerts</h2>
      <p class="text-dark">Stay updated with the latest security information in your area.</p>
      <div class="row mt-4 g-4">
        @foreach([1,2,3] as $i)
           <div class="row g-4">
      <!-- Card 1 -->
      <div class="col-md-6 col-lg-4">
        <div class="custom-card">
          <div class="position-relative">
            <div style="background:url('https://lh3.googleusercontent.com/aida-public/AB6AXuDpm9iKs4IAkZ16Wee_h6WPXm2L2qvHh-0ry8ay1GkFn3SjG_bcvULIF1WZSgbq0Hj9lvEAMN8lKwL4kND9LUwiQ6tC9OnWijHISo9_JS5qXjz5uuq8SWtPeGcp0BPJ4XuVbrHDGDTQdzD_bo-wHRnsiVFPDzhSVTp-_nP2pS3CXpIRyXKu2SL0boH4gx26_IJpw488TX5yQ886nCw3iggfwCAt9oG-XzOZ1PTuc1_S7IRPvHmMU5LlVlBLKHmGG0RNMoHVEn-evXWk') center/cover no-repeat; aspect-ratio:16/9;"></div>
            <div class="card-badge bg-primary">TRAFFIC</div>
          </div>
          <div class="p-3">
            <h5 class="fw-bold">Road Closure on Main Street</h5>
            <p class="small text-secondary">Due to ongoing construction, Main Street will be closed between Elm Avenue and Oak Street from 8 AM to 6 PM today. Please use alternative routes.</p>
          </div>
        </div>
      </div>
      <!-- Card 2 -->
      <div class="col-md-6 col-lg-4">
        <div class="custom-card">
          <div class="position-relative">
            <div style="background:url('https://lh3.googleusercontent.com/aida-public/AB6AXuBqR6rhoFDa5f0QnykJgLaLXiWY-mb53eJSbkg19TEGplVTssfvlGHerIvoGSKiO0IRDVQ4YWXWrh3jlGaNJ3h5oDe9t_tbfjPhLKDkcRtZ8_JdZBp7aqxu9732AU7H-xmgtryZXN9V5jK6d0pp1TjYyuUEZDfRHZvEJskjiytcnWNnN2jgBJv4bAYYb_Z8Oy0Vl82UtamvFxyz5gIPc5g4k_qQKyzbKXnfhur47nCwely_buVMySSAmrD_kHmnvvlTAmYh15sPl9aI') center/cover no-repeat; aspect-ratio:16/9;"></div>
            <div class="card-badge bg-warning">SAFETY</div>
          </div>
          <div class="p-3">
            <h5 class="fw-bold">Increased Patrols in Park District</h5>
            <p class="small text-secondary">Following recent incidents, police patrols have been increased in the Park District area. Residents are advised to remain vigilant.</p>
          </div>
        </div>
      </div>
      <!-- Card 3 -->
      <div class="col-md-6 col-lg-4">
        <div class="custom-card">
          <div class="position-relative">
            <div style="background:url('https://lh3.googleusercontent.com/aida-public/AB6AXuDGPTn9PdSU-O6IyOBGZw9AEUPwZZ_GsrCjxVX-9Ead92EiMqS0v0z7gkC1fgTbXhTe6bZJYB9apRm91PbB7cKJ1MixIlxWddN2n1jh3tWEKXmoAhyWMycH-8FGRRINbb_gGWKtA2bJJr3PxbiqC4ILC7JYCnbD7lR7pL0S2KFb98YIZC4XGD0CeW4jObUzPF8NafAqEhK1ISK8bwqgjWgtLyaRXHUbNXI3B6efvTrlS4vRJoDKjHjPaBgMBE5WDsyGgTGda7FG9ozY') center/cover no-repeat; aspect-ratio:16/9;"></div>
            <div class="card-badge bg-success">COMMUNITY</div>
          </div>
          <div class="p-3">
            <h5 class="fw-bold">Neighborhood Watch Meeting</h5>
            <p class="small text-secondary">Join us this Friday at 7 PM at the Community Center. Learn about crime prevention and connect with your neighbors.</p>
          </div>
        </div>
      </div>
        @endforeach
      </div>
    </div>
  </section>



@endsection
