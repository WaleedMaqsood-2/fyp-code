@extends('Public User.layouts.app')

@push('styles')
<link href="{{ asset('css/public user/dashboard.css') }}" rel="stylesheet">
@endpush
@section('title', 'User Dashboard')

@php
    if (!auth()->check()) {
        header('Location: ' . route('login'));
        exit;
    }
@endphp

@section('content')
  @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif
<!-- Main Content -->
<main class="container py-5">
  <div class="text-center mb-5">
    <h2 class="fw-bold display-5">Welcome back Sarah</h2>
    <p class="lead text-secondary">Your central hub for community safety and information.</p>
  </div>

  <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 mb-5">
    <button class="btn-custom btn-primary-custom px-4 py-2" >
      <span class="material-symbols-outlined me-2">add_comment</span><a class="text-decoration-none text-white" href="{{ route('public.complaints.form') }}"> Submit a New Complaint</a>
    </button>
    <button class="btn-custom btn-secondary-custom px-4 py-2" >
      <span class="material-symbols-outlined me-2">rule_folder</span><a class="text-decoration-none text-dark" href="{{ route('public.complaints.track') }}"> Track My Complaints</a>
    </button>
  </div>

  <div class="border-top pt-5">
    <h3 class="fw-bold text-center mb-4">Latest Public Alerts</h3>
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
    </div>
  </div>
</main>

@endsection