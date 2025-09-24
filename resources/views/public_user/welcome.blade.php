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
          <li class="nav-item  "><a class="nav-link fw-medium mx-lg-4 {{ Route::is('public.welcome') ? 'active' : '' }}" href="{{ route('public.welcome') }}">Home</a></li>
          <li class="nav-item  "><a class="nav-link fw-medium mx-lg-4 {{ Route::is('public.complaints.form') ? 'active' : '' }}" href="{{ route('public.complaints.form') }}">Complaints</a></li>
          <li class="nav-item  "><a class="nav-link fw-medium mx-lg-4 {{ Route::is('public.alerts') ? 'active' : '' }}" href="{{ route('public.alerts') }}">Alerts</a></li>
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
      <h1 class="display-4 fw-bold text-center">Welcome to 
        <span class="text-primary">CrimeWatch</span>
      </h1>
      <p class="lead mt-3 text-center">Your centralized platform for managing complaints, FIRs, evidence, and public alerts. Stay informed and secure with our AI-powered analysis.</p>
      <div class="mt-4 d-flex gap-3 flex-wrap login">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-lg-5">Login</a>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-lg-5">Sign Up</a>
      </div>
    </div>
  </section>

 
  <div class="border-top pt-5">
  <h3 class="fw-bold text-center mb-4">Latest Public Alerts</h3>
        <p class="text-dark text-center">Stay updated with the latest security information in your area.</p>
  <div class="row g-4">

    @include('public_user.partials.alerts-cards')

  </div>
</div>
<div class="text-center mt-4">
    <a href="{{ route('public.alerts') }}" class="btn btn-primary px-4">
        View All Alerts
    </a>
</div>


@endsection
