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
                <div class="card-header">
                    <h4 class="card-title">User Registrations</h4>
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Activities</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>User</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities ?? [] as $activity)
                                <tr>
                                    <td>{{ $activity->action }}</td>
                                    <td>{{ $activity->user->name }}</td>
                                    <td>{{ $activity->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No recent activity</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>{{-- row --}}

</div>{{-- page-inner --}}
</div>{{-- container --}}

@endsection
@endcan
@section('scripts')
<script>
    // Users chart
    new Chart(document.getElementById("usersChart"), {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May"],
            datasets: [{
                label: "New Users",
                data: [5, 10, 8, 15, 12],
                borderColor: "#177dff",
                backgroundColor: "rgba(23, 125, 255, 0.14)",
                fill: true
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