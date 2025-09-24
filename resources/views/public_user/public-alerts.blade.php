@extends('public_user.layouts.app')
@push('styles')
<link href="{{ asset('css/public_user/dashboard.css') }}" rel="stylesheet">
@endpush


  @section('content')
<body class="text-dark">
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


<!-- Main -->
<main>
  
  
  
  <div class="container py-5">
    <div class="text-center mb-5">
      <h1 class="fw-bold" style="font-family: 'Eczar', serif;">Public Alerts</h1>
      <p class="text-muted">Stay informed about recent incidents and safety warnings in your area.</p>
    </div>

<!-- Search + Filters -->
<div class="card shadow-sm mb-5">
    <div class="card-body">
       <form id="alertsFilterForm" class="row g-3 mt-4">
    <div class="col-12 col-md-4">
        <label for="search" class="form-label">Search Alerts</label>
        <input type="text" class="form-control" name="search" id="search" placeholder="Search by keyword" value="{{ request('search') }}">
    </div>

   <div class="col-12 col-md-4">
    <label for="alert-type" class="form-label">Alert Type</label>
    <select id="alert-type" name="type" class="form-select">
        <option value="all">All Types</option>
        @foreach($alertTypes as $type)
           <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
    {{ Str::title(str_replace('_', ' ', $type)) }}
</option>

        @endforeach
    </select>
</div>


    <div class="col-12 col-md-4">
        <label for="date-range" class="form-label">Date</label>
        <input type="date" name="date" id="date-range" class="form-control" value="{{ request('date') }}">
    </div>
  <!-- Reset Button -->
<button type="button" id="resetFilters" class="btn btn-secondary btn-lg w-100 d-flex align-items-center justify-content-center">
    <span class="material-symbols-outlined me-2">restart_alt</span> Reset
</button>



</form>

    </div>
</div>

<!-- Alerts Cards -->
<div id="alertsContainer">
    @include('public_user.partials.alerts-cards', ['alerts' => $alerts])
</div>


</div>
</main>

@endsection



<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('alertsFilterForm');
    const container = document.getElementById('alertsContainer');
    const searchInput = document.getElementById('search');
    const typeSelect = document.getElementById('alert-type');
    const dateInput = document.getElementById('date-range');
    const resetBtn = document.getElementById('resetFilters');

    let timeout = null; // debounce typing

    // Search input live AJAX
    searchInput.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => { fetchAlerts(); }, 500);
    });

    // Filter dropdown & date change
    [typeSelect, dateInput].forEach(el => {
        el.addEventListener('change', function() {
            fetchAlerts();
        });
    });

    // Reset button
    resetBtn.addEventListener('click', function() {
        form.reset();
        fetchAlerts();
    });

 
    // AJAX fetch function
    function fetchAlerts(url = null){
     url = url || "{{ route('public.alerts') }}";

    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();

    // check if URL already has "?" (from pagination)
    if(url.includes('?')){
        url += '&' + params;
    } else {
        url += '?' + params;
    }

    fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} })
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html; // partial includes pagination
        window.history.pushState(null,'', url);
        attachPaginationLinks();
    });
    }



 function attachPaginationLinks(){
        document.querySelectorAll('#paginationLinks a').forEach(link => {
            link.addEventListener('click', function(e){
                e.preventDefault();
                fetchAlerts(this.href);
            });
        });
    }
    // Initial load
    fetchAlerts();
});
</script>

