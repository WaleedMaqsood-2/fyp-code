@extends('public_user.layouts.app')

@push('styles')
<link href="{{ asset('css/public_user/dashboard.css') }}" rel="stylesheet">

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
<main class="container py-2 pb-5">
  <!-- Hero -->
  <section class="hero-section d-flex align-items-center">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center text-sm-start">
      <h1 class="display-4 fw-bold text-center">Welcome back 
        <span class="text-primary">{{ Auth::user()->name }}</span>
      </h1>
      <p class="lead mt-3 text-center">Your centralized platform for managing complaints, FIRs, evidence, and public alerts. Stay informed and secure with our AI-powered analysis.</p>
      <div class="d-flex gap-3 flex-wrap login mt-5">
      <button class="btn-custom btn-primary-custom px-4 py-2" >
      <span class="material-symbols-outlined me-2">add_comment</span><a class="text-decoration-none text-white" href="{{ route('public.complaints.form') }}"> Submit a New Complaint</a>
    </button>
    <button class="btn-custom btn-secondary-custom px-4 py-2" >
      <span class="material-symbols-outlined me-2">rule_folder</span><a class="text-decoration-none text-dark" href="{{ route('public.complaints.track') }}"> Track My Complaints</a>
    </button>
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

</main>

@endsection