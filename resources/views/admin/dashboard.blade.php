@extends('layouts.master')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
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
  <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-left">
            <h1 class="dashboard-title">Admin Dashboard</h1>
            <p class="dashboard-subtitle">Welcome back, {{ auth()->user()->name ?? 'Admin' }}</p>
        </div>
        <div class="header-right">
            <div class="date-display">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>
    </div>
    
       <div class="row">
       <!-- Stats Cards -->
    <div class="stats-grid ">
        <div class="stat-card primary-gradient">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Users</p>
                <h3 class="stat-number">{{ $totalUsers ?? 0 }}</h3>
            </div>
        </div>

        <div class="stat-card info-gradient">
            <div class="stat-icon">
                <i class="fas fa-images"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Media Files</p>
                <h3 class="stat-number">{{ $totalMedia ?? 0 }}</h3>
               
            </div>
        </div>

        <div class="stat-card warning-gradient">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Complaints</p>
                <h3 class="stat-number">{{ $totalComplaints ?? 0 }}</h3>
              
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

        {{-- <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">AI Usage Breakdown</h4>
                </div>
                <div class="card-body">
                    <canvas id="aiUsageChart"></canvas>
                </div>
            </div>
        </div> --}}
        <div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Complaints Breakdown</h4>
        </div>
        <div class="card-body">
            <canvas id="complaintsChart"></canvas>
        </div>
    </div>
</div>

    </div>{{-- row --}}

  <!-- RECENT ACTIVITY -->
   <!-- Recent Activities -->
    <div class="activities-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-history me-2"></i>
                Recent Activities
            </h3>
           
        </div>
        
        <div class="activities-list">
            @forelse($recentActivities ?? [] as $activity)
            <div class="activity-item">
                <div class="activity-icon">
                    @if(str_contains(strtolower($activity->action), 'login'))
                        <i class="fas fa-sign-in-alt"></i>
                    @elseif(str_contains(strtolower($activity->action), 'create'))
                        <i class="fas fa-plus-circle"></i>
                    @elseif(str_contains(strtolower($activity->action), 'update'))
                        <i class="fas fa-edit"></i>
                    @elseif(str_contains(strtolower($activity->action), 'delete'))
                        <i class="fas fa-trash"></i>
                    @else
                        <i class="fas fa-circle"></i>
                    @endif
                </div>
                <div class="activity-content">
                    <div class="activity-action">
                        {{ $activity->action }}
                    </div>
                    <div class="activity-user">
                        @if(!empty($activity->user->profile_image))
                            <img src="{{ asset('storage/' . $activity->user->profile_image) }}" 
                                 alt="Profile" 
                                 class="user-avatar">
                        @else
                            <div class="user-avatar default">
                                {{ strtoupper(substr($activity->user->name ?? 'N', 0, 1)) }}
                            </div>
                        @endif
                        <span class="user-name">{{ $activity->user->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="activity-time">
                    <i class="fas fa-clock me-1"></i>
                    {{ $activity->created_at->format('d M Y H:i') }}
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h4>No recent activity</h4>
                <p>Activities will appear here as they happen</p>
            </div>
            @endforelse
        </div>
         <div class="section-actions">
                @if($showAll)
                    <a href="{{ request()->url() }}" class="btn btn-outline">
                        <i class="fas fa-eye-slash me-1"></i> Hide
                    </a>
                @else
                    {{ $recentActivities->links() }}
                    <a href="{{ request()->fullUrlWithQuery(['show' => 'all']) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-eye me-1"></i> Show All
                    </a>
                @endif
            </div>
    </div>


</div>{{-- page-inner --}}
</div>{{-- container --}}

@endsection
@endcan
{{-- @section('scripts')
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
   /*
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
    */
   // Complaints Breakdown Chart
new Chart(document.getElementById("complaintsChart"), {
    type: 'pie',
    data: {
        labels: ["Resolved", "In-Progress", "Pending", "Rejected"],
        datasets: [{
            data: [40, 25, 20, 15], 
            backgroundColor: ["#177dff", "#f3545d", "#ffa534", "#1dd1a1"]
        }]
    }
});

</script>
@endsection --}}



@section('scripts')
<script>
    // Users chart
    const months = @json($months);
    const userCounts = @json($userCounts);

    const usersCtx = document.getElementById('usersChart').getContext('2d');
    new Chart(usersCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: "New Users",
                data: userCounts,
                borderColor: "#667eea",
                backgroundColor: "rgba(102, 126, 234, 0.1)",
                borderWidth: 3,
                pointBackgroundColor: "#667eea",
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 14
                    },
                    padding: 12,
                    cornerRadius: 6
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Complaints Breakdown Chart
    const complaintsCtx = document.getElementById('complaintsChart').getContext('2d');
    new Chart(complaintsCtx, {
        type: 'doughnut',
        data: {
            labels: ["Resolved", "In-Progress", "Pending", "Rejected"],
            datasets: [{
                data: [40, 25, 20, 15],
                backgroundColor: [
                    "#667eea",
                    "#f3545d",
                    "#ffa534",
                    "#1dd1a1"
                ],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 13
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });

    // Alert close functionality
    document.querySelectorAll('.alert-close').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.alert-modern').style.opacity = '0';
            setTimeout(() => {
                this.closest('.alert-modern').style.display = 'none';
            }, 300);
        });
    });

    // Auto-hide success alerts after 5 seconds
    @if(session('success'))
    setTimeout(() => {
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 300);
        }
    }, 5000);
    @endif
</script>
@endsection




@php
  $searchAction = route('admin.user.search'); 
  $searchPlaceholder = 'Search Dashboard...';
@endphp