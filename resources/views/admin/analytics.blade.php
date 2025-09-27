@extends('layouts.master')
@section('content')
<div class="container">
     <div class="ms-2 mt-5">
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

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 ">Analytics Dashboard</h1>
        <div class="d-flex gap-2 flex-wrap">
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Last 30 Days
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                    <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                    <li><a class="dropdown-item" href="#">Last Year</a></li>
                </ul>
            </div>
            <button class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#reportModal">
                <span class="material-symbols-outlined">add</span> Generate Report
            </button>
        </div>
    </div>

    {{-- Analytics Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card bg-gray-800 text-white shadow-sm rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-muted mb-1 small">Total Complaints</p>
                        <h4 class="mb-0">1,287</h4>
                    </div>
                    <div class="bg-primary rounded-circle p-2">
                        <span class="material-symbols-outlined text-white fs-4">assignment</span>
                    </div>
                </div>
                <small class="text-success d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined fs-6">arrow_upward</span> 12% from last month
                </small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card bg-gray-800 text-white shadow-sm rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-muted mb-1 small">Solved Cases</p>
                        <h4 class="mb-0">972</h4>
                    </div>
                    <div class="bg-success rounded-circle p-2">
                        <span class="material-symbols-outlined text-white fs-4">task_alt</span>
                    </div>
                </div>
                <small class="text-success d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined fs-6">arrow_upward</span> 5% from last month
                </small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card bg-gray-800 text-white shadow-sm rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-muted mb-1 small">Pending Cases</p>
                        <h4 class="mb-0">315</h4>
                    </div>
                    <div class="bg-warning rounded-circle p-2">
                        <span class="material-symbols-outlined text-white fs-4">pending</span>
                    </div>
                </div>
                <small class="text-danger d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined fs-6">arrow_downward</span> 2% from last month
                </small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card bg-gray-800 text-white shadow-sm rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-muted mb-1 small">Avg. Resolution Time</p>
                        <h4 class="mb-0">24.5h</h4>
                    </div>
                    <div class="bg-danger rounded-circle p-2">
                        <span class="material-symbols-outlined text-white fs-4">hourglass_top</span>
                    </div>
                </div>
                <small class="text-success d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined fs-6">arrow_upward</span> -1.2h from last month
                </small>
            </div>
        </div>
    </div>

   


    {{-- Charts --}}
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card  shadow-sm rounded-3 p-3 h-100">
            <h5 class="mb-3">Complaint Trends</h5>
            <canvas id="complaintChart" class="w-100" style="height:300px;"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card  shadow-sm rounded-3 p-3 h-100">
            <h5 class="mb-3">Case Status Distribution</h5>
            <canvas id="statusChart" class="w-100" style="height:300px;"></canvas>
        </div>
    </div>
</div>

{{-- AI Report Feedback & Ratings --}}
<div class="row g-4 mb-4">
    <!-- Ratings Summary -->
    <div class="col-lg-4">
        <div class="card  shadow-sm rounded-3 p-3 h-100">
            <h5 class="mb-3">Aggregated Ratings</h5>
            <div class="d-flex align-items-center mb-2">
                <span class="fs-1 fw-bold me-2">4.2</span>
                <div>
                    <span class="text-warning material-symbols-outlined">star</span>
                    <span class="text-warning material-symbols-outlined">star</span>
                    <span class="text-warning material-symbols-outlined">star</span>
                    <span class="text-warning material-symbols-outlined">star</span>
                    <span class="text-secondary material-symbols-outlined">star</span>
                </div>
            </div>
            <p class="small text-muted">Based on 215 ratings</p>

            {{-- Progress Bars --}}
            <div class="mt-3">
                <div class="d-flex align-items-center mb-2">
                    <small class="text-muted me-2" style="width:50px">5 star</small>
                    <div class="progress flex-grow-1">
                        <div class="progress-bar bg-success" style="width:60%"></div>
                    </div>
                    <small class="ms-2">129</small>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <small class="text-muted me-2" style="width:50px">4 star</small>
                    <div class="progress flex-grow-1">
                        <div class="progress-bar bg-primary" style="width:25%"></div>
                    </div>
                    <small class="ms-2">54</small>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <small class="text-muted me-2" style="width:50px">3 star</small>
                    <div class="progress flex-grow-1">
                        <div class="progress-bar bg-warning" style="width:8%"></div>
                    </div>
                    <small class="ms-2">17</small>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <small class="text-muted me-2" style="width:50px">2 star</small>
                    <div class="progress flex-grow-1">
                        <div class="progress-bar bg-orange" style="width:5%"></div>
                    </div>
                    <small class="ms-2">11</small>
                </div>
                <div class="d-flex align-items-center">
                    <small class="text-muted me-2" style="width:50px">1 star</small>
                    <div class="progress flex-grow-1">
                        <div class="progress-bar bg-danger" style="width:2%"></div>
                    </div>
                    <small class="ms-2">4</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Feedback -->
    <div class="col-lg-8">
        <div class="card  shadow-sm rounded-3 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Recent Feedback</h5>
                <button class="btn btn-sm btn-outline-primary">View All</button>
            </div>
            <div class="overflow-auto" style="max-height:250px">
                <!-- Feedback Item -->
                <div class="p-3 mb-3 shadow-sm rounded">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-semibold mb-1">"Inaccurate data mapping in Section 3"</p>
                            <small class="text-muted">Report: Q1 Forensic Analysis | User: @analyst_jane</small>
                        </div>
                        <span class="badge bg-warning text-dark">3/5</span>
                    </div>
                    <button class="btn btn-link btn-sm text-primary p-0 mt-2">Drill Down</button>
                </div>

                <div class="p-3 mb-3 shadow-sm rounded">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-semibold mb-1">"Very comprehensive and well-structured."</p>
                            <small class="text-muted">Report: Monthly Crime Report - April | User: @police_chief</small>
                        </div>
                        <span class="badge bg-success">5/5</span>
                    </div>
                    <button class="btn btn-link btn-sm text-primary p-0 mt-2">Drill Down</button>
                </div>

                <div class="p-3 shadow-sm rounded">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-semibold mb-1">"Could use more visualizations for trend data."</p>
                            <small class="text-muted">Report: Weekly Performance Metrics | User: @admin_user</small>
                        </div>
                        <span class="badge bg-info text-dark">4/5</span>
                    </div>
                    <button class="btn btn-link btn-sm text-primary p-0 mt-2">Drill Down</button>
                </div>
            </div>
        </div>
    </div>
</div>



    {{-- Recent Reports Table --}}
    <div class="card  shadow-sm rounded-3 mb-4">
        <div class="p-3 text-center bg-dark text-white">
            <h5>Recent Reports</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle text-white">
                <thead class="table-dark text-dark">
                    <tr>
                        <th>Report Name</th>
                        <th>Date Range</th>
                        <th>Generated By</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Monthly Crime Report - April</td>
                        <td>01/04/2024 - 30/04/2024</td>
                        <td>Admin</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>
                            <button class="btn btn-link text-primary p-0"><span class="material-symbols-outlined fs-6">download</span> Download</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Weekly Performance Metrics</td>
                        <td>15/05/2024 - 21/05/2024</td>
                        <td>System</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>
                            <button class="btn btn-link text-primary p-0"><span class="material-symbols-outlined fs-6">download</span> Download</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Forensic Analysis Backlog</td>
                        <td>01/01/2024 - 22/05/2024</td>
                        <td>Forensic Head</td>
                        <td><span class="badge bg-warning text-dark">Generating</span></td>
                        <td>
                            <button class="btn btn-link text-muted p-0 disabled"><span class="material-symbols-outlined fs-6">hourglass_top</span> Pending</button>
                        </td>
                    </tr>
                    <tr>
                        <td>User Activity Log</td>
                        <td>22/05/2024</td>
                        <td>Admin</td>
                        <td><span class="badge bg-danger">Failed</span></td>
                        <td>
                            <button class="btn btn-link text-primary p-0"><span class="material-symbols-outlined fs-6">refresh</span> Retry</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Report Modal --}}
    <div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-gray-800 text-white rounded-3">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Generate Custom Report</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="reportName" class="form-label">Report Name</label>
                            <input type="text" class="form-control bg-secondary text-white" id="reportName" placeholder="Monthly Crime Report">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control bg-secondary text-white" id="startDate">
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control bg-secondary text-white" id="endDate">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="complaintType" class="form-label">Complaint Type</label>
                            <select class="form-select bg-secondary text-white" id="complaintType">
                                <option selected>All Types</option>
                                <option value="theft">Theft</option>
                                <option value="assault">Assault</option>
                                <option value="vandalism">Vandalism</option>
                                <option value="fraud">Fraud</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Generate & Export</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxComplaint = document.getElementById('complaintChart').getContext('2d');
    new Chart(ctxComplaint, {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul'],
            datasets: [{
                label: 'Total Complaints',
                data: [120, 150, 180, 200, 170, 190, 210],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { color: '#e5e7eb' } } },
            scales: {
                x: { ticks: { color: '#e5e7eb' }, grid: { color: 'rgba(255,255,255,0.1)' } },
                y: { ticks: { color: '#e5e7eb' }, grid: { color: 'rgba(255,255,255,0.1)' } }
            }
        }
    });

    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'pie',
        data: {
            labels: ['Solved', 'Pending', 'Failed'],
            datasets: [{ data: [972, 315, 45], backgroundColor: ['#22c55e','#eab308','#ef4444'] }]
        },
        options: { responsive: true, plugins: { legend: { labels: { color: '#e5e7eb' } } } }
    });
</script>
@endsection





@php
  $searchAction = route('admin.analytics.search');
  $searchPlaceholder = 'Search Analytics...';
@endphp