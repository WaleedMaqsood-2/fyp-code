@extends('layouts.master')
@section('content')
<div class="container">
     <div class="ms-2 mt-4">
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
  

  <main class="container py-5">
    <div class="mx-auto" style="max-width: 900px;">
      
      <!-- Header -->
      <header class="mb-4 text-center">
        <h1 class="fw-bold text-dark">AI Report Feedback</h1>
        <p class="text-muted">Review and manage feedback on AI-generated reports</p>
      </header>

      <!-- Filters Section -->
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <form class="row g-3 align-items-center">
            <!-- Search -->
            <div class="col-md-4">
              <input type="search" class="form-control" placeholder="Search feedback...">
            </div>
            <!-- Status -->
            <div class="col-md-3">
              <select class="form-select">
                <option selected>Status</option>
                <option>All</option>
                <option>Reviewed</option>
                <option>Pending</option>
              </select>
            </div>
            <!-- Date -->
            <div class="col-md-3">
              <input type="date" class="form-control">
            </div>
            <!-- Button -->
            <div class="col-md-2 d-grid">
              <button type="submit" class="btn btn-primary">Filter</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Feedback List -->
      <div class="d-flex flex-column gap-3">
        
        <!-- Feedback Item -->
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="d-flex align-items-center gap-3">
                  <h5 class="fw-bold ">Report Accuracy</h5>
  <!-- Stars Rating -->
        <div class="fs-4 mb-2">
          <span class="text-warning">&#9733;</span>
          <span class="text-warning">&#9733;</span>
          <span class="text-warning">&#9733;</span>
          <span class="text-secondary">&#9733;</span>
          <span class="text-secondary">&#9733;</span>
        </div>
                </div>
                <p class="text-muted small mb-2">Submitted on: 2025-09-25</p>
                <p class="mb-2">The AI-generated report missed some critical points about the case. Please improve accuracy in future reports.</p>
                <span class="badge bg-warning text-dark">Pending</span>
              </div>
              <button class="btn btn-outline-primary btn-sm">Review</button>
            </div>
          </div>
        </div>

        <!-- Feedback Item -->
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="d-flex gap-3 align-items-center">
                  <h5 class="fw-bold ">Formatting Issues</h5>
                    <!-- Stars Rating -->
        <div class="fs-4 mb-2">
          <span class="text-warning">&#9733;</span>
          <span class="text-warning">&#9733;</span>
          <span class="text-warning">&#9733;</span>
          <span class="text-warning">&#9733;</span>
          <span class="text-secondary">&#9733;</span>
        </div>
                </div>
                <p class="text-muted small mb-2">Submitted on: 2025-09-24</p>
                <p class="mb-2">The document formatting is inconsistent. Headings and paragraphs need better alignment.</p>
                <span class="badge bg-success">Reviewed</span>
              </div>
              <button class="btn btn-outline-primary btn-sm">Review</button>
            </div>
          </div>
        </div>

        <!-- Feedback Item -->
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="d-flex align-items-center gap-3">

                  <h5 class="fw-bold">Clarity and Readability</h5>
                    <!-- Stars Rating -->
        <div class="fs-4 mb-2">
          <span class="text-warning">&#9733;</span>
          <span class="text-warning">&#9733;</span>
          <span class="text-warning">&#9733;</span>
          <span class="text-secondary">&#9733;</span>
          <span class="text-secondary">&#9733;</span>
        </div>
                </div>
                <p class="text-muted small mb-2">Submitted on: 2025-09-23</p>
                <p class="mb-2">Some sentences are too complex and difficult to understand. Simplify the language for better readability.</p>
                <span class="badge bg-warning text-dark">Pending</span>
              </div>
              
              <button class="btn btn-outline-primary btn-sm">Review</button>
            </div>
          </div>
        </div>

      </div>

    </div>
  </main>



</div>
</div>

@endsection

@php
  $searchConfig = [
    'endpoint' => route('admin.ai.search'),
    'suggestionKey' => 'feedback',
    'resultKey' => 'feedback',
  ];
@endphp

<script>
  window.searchConfig = @json($searchConfig);
</script>
@php
  $searchAction = route('admin.ai.search');
  $searchPlaceholder = 'Search AI Feedback...';
@endphp