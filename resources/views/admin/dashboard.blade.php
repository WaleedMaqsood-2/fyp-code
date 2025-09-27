@extends('layouts.master')

@section('title', 'Admin Dashboard')

@php
    if (!auth()->check()) {
        header('Location: ' . route('login'));
        exit;
    }
@endphp
@can('admin')
@section('content')
<div class="container">
    <div class="page-inner">
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

    
       <div class="row">
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Total Users</p>
                                <h4 class="card-title">{{ $totalUsers ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-images text-info"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Media Files</p>
                                <h4 class="card-title">{{ $totalMedia ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-file-alt text-warning"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Pending Summaries</p>
                                <h4 class="card-title">{{ $pendingSummaries ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-robot text-success"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">AI Usage</p>
                                <h4 class="card-title">{{ $aiUsage ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- row --}}

    <!-- CHARTS -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">User Registrations</h4>

        <!-- Year Filter -->
        <form method="GET" action="{{ route('dashboard') }}">
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($years as $yearOption)
                    <option value="{{ $yearOption }}" 
                        {{ $selectedYear == $yearOption ? 'selected' : '' }}>
                        {{ $yearOption }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body">
        <canvas id="usersChart"></canvas>
    </div>
</div>

        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">AI Usage Breakdown</h4>
                </div>
                <div class="card-body">
                    <canvas id="aiUsageChart"></canvas>
                </div>
            </div>
        </div>
    </div>{{-- row --}}

  <!-- RECENT ACTIVITY -->
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 text-primary fw-bold">
                    <i class="bi bi-activity me-2"></i> Recent Activities
                </h4>
            </div>
            <div class="card-body p-4">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Action</th>
                                <th scope="col">User</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities ?? [] as $activity)
                                <tr>
                                    <td>
                                        <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                            {{ $activity->action }}
                                        </span>
                                    </td>
                    <td>
    <div class="d-flex align-items-center">
        @if(!empty($activity->user->profile_image))
            {{-- Profile Image from storage --}}
            <img src="{{ asset('storage/' . $activity->user->profile_image) }}" 
                 alt="Profile" 
                 class="rounded-circle me-2" 
                 style="width:35px; height:35px; object-fit:cover;">
        @else
            {{-- Default avatar (first letter) --}}
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-2" 
                 style="width:35px; height:35px;">
                {{ strtoupper(substr($activity->user->name ?? 'N/A', 0, 1)) }}
            </div>
        @endif

        <span class="fw-semibold">{{ $activity->user->name ?? 'N/A' }}</span>
    </div>
</td>

                                    <td class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $activity->created_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        No recent activity
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination / Show All / Hide --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    @if($showAll)
                        <div class="ms-auto">
                            {{-- Hide button --}}
                            <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                                <i class="bi bi-eye-slash me-1"></i> Hide
                            </a>
                        </div>
                    @else
                        <div>
                            {{ $recentActivities->links() }}
                        </div>
                        <a href="{{ request()->fullUrlWithQuery(['show' => 'all']) }}" 
                           class="btn btn-outline-primary btn-sm rounded-pill px-4">
                            <i class="bi bi-eye me-1"></i> Show All
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>


</div>{{-- page-inner --}}
</div>{{-- container --}}

@endsection
@endcan
@section('scripts')
<script>
    // Users chart
   const months = @json($months);
    const userCounts = @json($userCounts);

    new Chart(document.getElementById("usersChart"), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: "New Users",
                data: userCounts,
                borderColor: "#177dff",
                backgroundColor: "rgba(23, 125, 255, 0.14)",
                fill: true,
                tension: 0.5 // smooth line
            }]
        }
    });
    // AI usage chart
    new Chart(document.getElementById("aiUsageChart"), {
        type: 'pie',
        data: {
            labels: ["Speech-to-Text", "Face Detection", "Summarization"],
            datasets: [{
                data: [55, 30, 15],
                backgroundColor: ["#177dff", "#f3545d", "#ffa534"]
            }]
        }
    });
</script>
@endsection



@php
  $searchAction = route('admin.user.search'); 
  $searchPlaceholder = 'Search Dashboard...';
@endphp